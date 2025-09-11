<?php
// auto_cancel_unpaid.php
date_default_timezone_set('Asia/Colombo');

// Load DB connection
require_once __DIR__ . '/config.php';

// Enhanced logging with error levels
$logFile = __DIR__ . '/logs/auto_cancel.log';
$errorLogFile = __DIR__ . '/logs/auto_cancel_errors.log';

if (!is_dir(__DIR__ . '/logs')) {
    if (!mkdir(__DIR__ . '/logs', 0755, true)) {
        die("ERROR: Cannot create logs directory\n");
    }
}

function logLine($msg, $level = 'INFO') {
    global $logFile, $errorLogFile;
    $timestamp = date("Y-m-d H:i:s");
    $line = "[$timestamp] [$level] $msg\n";
    echo $line;
    
    // Log to main file
    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    
    // Log errors to separate file
    if ($level === 'ERROR' || $level === 'CRITICAL') {
        file_put_contents($errorLogFile, $line, FILE_APPEND | LOCK_EX);
    }
}

function handleError($errno, $errstr, $errfile, $errline) {
    logLine("PHP Error [$errno]: $errstr in $errfile:$errline", 'ERROR');
}

function handleException($exception) {
    logLine("Uncaught Exception: " . $exception->getMessage() . " in " . 
            $exception->getFile() . ":" . $exception->getLine(), 'CRITICAL');
}

// Set error handlers
set_error_handler('handleError');
set_exception_handler('handleException');

logLine("=== Auto-cancel script started ===");

try {
    // Validate database connection
    if (!isset($conn) || $conn->connect_error) {
        throw new Exception("Database connection failed: " . ($conn->connect_error ?? 'Connection not initialized'));
    }
    
    logLine("Database connection verified");

    // Time validation - Fixed the time check logic
    $now = new DateTime();
    $currentHour = (int)$now->format('H');
    $currentMinute = (int)$now->format('i');
    
    // Check if it's 7 PM (19:00) or later
    if ($currentHour < 19) {
        logLine("Current time is " . $now->format('H:i') . ". Script should run at 19:00 or later. Exiting.", 'INFO');
        exit(0);
    }
    
    logLine("Time check passed. Current time: " . $now->format('H:i:s'));

    // Check if bulk_reservations table exists
    $checkBulkTable = $conn->query("SHOW TABLES LIKE 'bulk_reservations'");
    $hasBulkTable = ($checkBulkTable && $checkBulkTable->num_rows > 0);
    
    if ($hasBulkTable) {
        logLine("Bulk reservations table detected - including bulk reservations in processing");
    }

    // Check if bulk_reservation_id column exists in reservations table
    $hasBulkColumn = false;
    if ($hasBulkTable) {
        $checkColumn = $conn->query("SHOW COLUMNS FROM reservations LIKE 'bulk_reservation_id'");
        $hasBulkColumn = ($checkColumn && $checkColumn->num_rows > 0);
        logLine("Bulk reservation column in reservations table: " . ($hasBulkColumn ? 'Found' : 'Not found'));
    }

    // Enhanced query to handle both regular and bulk reservations
    $query = "
        SELECT 
            r.reservation_id, 
            r.room_id, 
            r.amount, 
            r.customer_name,
            " . ($hasBulkColumn ? "r.bulk_reservation_id," : "NULL as bulk_reservation_id,") . "
            r.check_in_date,
            r.status as current_status,
            COALESCE(p.status, 'none') as payment_status,
            'regular' as reservation_source
        FROM reservations r
        LEFT JOIN payments p ON r.reservation_id = p.reservation_id 
        WHERE r.check_in_date = CURDATE()
          AND r.status IN ('booked', 'confirmed', 'pending')
          AND (
              p.payment_id IS NULL 
              OR p.status IS NULL 
              OR LOWER(TRIM(p.status)) NOT IN ('paid', 'completed', 'success')
          )
    ";
    
    // Add bulk reservations if table exists
    if ($hasBulkTable) {
        $query .= "
        UNION ALL
        SELECT 
            br.bulk_reservation_id as reservation_id,
            0 as room_id,
            br.total_amount as amount,
            br.customer_name,
            br.bulk_reservation_id,
            br.check_in_date,
            br.status as current_status,
            COALESCE(bp.status, 'none') as payment_status,
            'bulk' as reservation_source
        FROM bulk_reservations br
        LEFT JOIN bulk_payments bp ON br.bulk_reservation_id = bp.bulk_reservation_id
        WHERE br.check_in_date = CURDATE()
          AND br.status IN ('booked', 'confirmed', 'pending')
          AND (
              bp.payment_id IS NULL 
              OR bp.status IS NULL 
              OR LOWER(TRIM(bp.status)) NOT IN ('paid', 'completed', 'success')
          )
        ";
    }
    
    $query .= " ORDER BY reservation_id";

    logLine("Executing reservation query...");
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception("Failed to fetch reservations: " . $conn->error);
    }

    $reservations = [];
    $bulkReservations = [];
    
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
        
        // Separate bulk reservations for different handling
        if ($row['reservation_source'] === 'bulk') {
            $bulkReservations[] = $row;
        }
    }

    $totalCount = count($reservations);
    $bulkCount = count($bulkReservations);
    
    logLine("Found $totalCount total unpaid reservation(s) to cancel");
    if ($bulkCount > 0) {
        logLine("Including $bulkCount bulk reservation(s)");
    }

    if ($totalCount === 0) {
        logLine("No reservations to cancel. Script completed successfully.");
        exit(0);
    }

    // Prepare statements for regular reservations
    $updateStmt = $conn->prepare("
        UPDATE reservations 
        SET status = 'cancelled', 
            cancelled_at = NOW(), 
            cancelled_by_scheduler = 1,
            cancellation_reason = 'Auto-cancelled: No payment received by 7 PM'
        WHERE reservation_id = ?
    ");
    
    if (!$updateStmt) {
        throw new Exception("Failed to prepare update statement: " . $conn->error);
    }

    // Prepare statements for bulk reservations (if table exists)
    $bulkUpdateStmt = null;
    if ($hasBulkTable) {
        $bulkUpdateStmt = $conn->prepare("
            UPDATE bulk_reservations 
            SET status = 'cancelled', 
                cancelled_at = NOW(),
                cancelled_by_scheduler = 1,
                cancellation_reason = 'Auto-cancelled: No payment received by 7 PM'
            WHERE bulk_reservation_id = ?
        ");
        
        if (!$bulkUpdateStmt) {
            logLine("Warning: Failed to prepare bulk update statement: " . $conn->error, 'WARN');
        }
    }

    $billingStmt = $conn->prepare("
        INSERT INTO billing (
            reservation_id, 
            amount, 
            billing_type, 
            status, 
            billing_note, 
            created_at
        ) VALUES (?, ?, 'no-show-fee', 'pending', ?, NOW())
    ");
    
    if (!$billingStmt) {
        throw new Exception("Failed to prepare billing statement: " . $conn->error);
    }

    // Begin transaction with proper error handling
    if (!$conn->autocommit(false)) {
        throw new Exception("Failed to disable autocommit: " . $conn->error);
    }
    
    if (!$conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE)) {
        throw new Exception("Failed to start transaction: " . $conn->error);
    }
    
    logLine("Transaction started");

    $successCount = 0;
    $errorCount = 0;
    $bulkSuccessCount = 0;

    foreach ($reservations as $res) {
        try {
            $rid = (int)$res['reservation_id'];
            $roomId = (int)$res['room_id'];
            $amount = (float)$res['amount'];
            $customerName = $res['customer_name'] ?? 'Unknown';
            $reservationType = $res['reservation_source'];
            $bulkId = $res['bulk_reservation_id'] ?? null;
            
            // Validate data
            if ($rid <= 0) {
                throw new Exception("Invalid reservation ID: $rid");
            }
            
            if ($amount < 0) {
                logLine("Warning: Negative amount ($amount) for reservation $rid", 'WARN');
            }

            $note = sprintf(
                "Auto-cancelled at %s - No payment received for check-in date %s. Customer: %s (%s reservation)",
                $now->format('Y-m-d H:i:s'),
                $res['check_in_date'],
                $customerName,
                $reservationType
            );

            // Handle different reservation types
            if ($reservationType === 'bulk') {
                // Update bulk reservation
                if ($bulkUpdateStmt) {
                    $bulkUpdateStmt->bind_param('i', $rid);
                    if (!$bulkUpdateStmt->execute()) {
                        throw new Exception("Failed to update bulk reservation $rid: " . $bulkUpdateStmt->error);
                    }
                    
                    if ($bulkUpdateStmt->affected_rows === 0) {
                        logLine("Warning: No rows updated for bulk reservation $rid (may already be processed)", 'WARN');
                    } else {
                        logLine("Bulk reservation $rid cancelled successfully");
                        $bulkSuccessCount++;
                    }
                }
                
                // Also cancel individual reservations in this bulk group if they exist and column exists
                if ($bulkId && $hasBulkTable && $hasBulkColumn) {
                    $cancelIndividualStmt = $conn->prepare("
                        UPDATE reservations 
                        SET status = 'cancelled', 
                            cancelled_at = NOW(), 
                            cancelled_by_scheduler = 1,
                            cancellation_reason = 'Auto-cancelled: Bulk reservation payment not received'
                        WHERE bulk_reservation_id = ? AND status IN ('booked', 'confirmed', 'pending')
                    ");
                    
                    if ($cancelIndividualStmt) {
                        $cancelIndividualStmt->bind_param('i', $bulkId);
                        $cancelIndividualStmt->execute();
                        $affectedIndividual = $cancelIndividualStmt->affected_rows;
                        if ($affectedIndividual > 0) {
                            logLine("Cancelled $affectedIndividual individual reservations in bulk group $bulkId");
                        }
                        $cancelIndividualStmt->close();
                    }
                }
            } else {
                // Update regular reservation
                $updateStmt->bind_param('i', $rid);
                if (!$updateStmt->execute()) {
                    throw new Exception("Failed to update reservation $rid: " . $updateStmt->error);
                }
                
                if ($updateStmt->affected_rows === 0) {
                    logLine("Warning: No rows updated for reservation $rid (may already be processed)", 'WARN');
                } else {
                    logLine("Regular reservation $rid cancelled successfully");
                }
            }

            // Create billing record (if amount > 0)
            if ($amount > 0) {
                $billingStmt->bind_param('ids', $rid, $amount, $note);
                if (!$billingStmt->execute()) {
                    throw new Exception("Failed to create billing for reservation $rid: " . $billingStmt->error);
                }
                logLine("Billing record created: Reservation $rid, Amount: $amount ($reservationType)");
            }

            $successCount++;
            
        } catch (Exception $e) {
            $errorCount++;
            logLine("Error processing reservation $rid: " . $e->getMessage(), 'ERROR');
            // Continue with other reservations instead of failing completely
        }
    }

    // Commit or rollback based on results
    if ($errorCount === 0) {
        $conn->commit();
        logLine("Transaction committed successfully. Processed $successCount reservations ($bulkSuccessCount bulk).");
    } else if ($successCount > 0 && $errorCount < $totalCount) {
        // Partial success - you might want to commit or rollback based on business rules
        $conn->commit();
        logLine("Transaction committed with partial success: $successCount successful ($bulkSuccessCount bulk), $errorCount failed.", 'WARN');
    } else {
        $conn->rollback();
        logLine("Transaction rolled back due to errors: $errorCount failed out of $totalCount", 'ERROR');
    }

} catch (Exception $e) {
    // Rollback transaction if active
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->rollback();
            logLine("Transaction rolled back due to critical error", 'ERROR');
        } catch (Exception $rollbackError) {
            logLine("Failed to rollback transaction: " . $rollbackError->getMessage(), 'CRITICAL');
        }
    }
    
    logLine("CRITICAL ERROR: " . $e->getMessage(), 'CRITICAL');
    exit(1);
    
} finally {
    // Cleanup resources
    if (isset($updateStmt) && $updateStmt instanceof mysqli_stmt) {
        $updateStmt->close();
    }
    if (isset($billingStmt) && $billingStmt instanceof mysqli_stmt) {
        $billingStmt->close();
    }
    if (isset($bulkUpdateStmt) && $bulkUpdateStmt instanceof mysqli_stmt) {
        $bulkUpdateStmt->close();
    }
    if (isset($conn) && $conn instanceof mysqli) {
        try {
            $conn->autocommit(true); // Reset autocommit
            $conn->close();
        } catch (Exception $cleanupError) {
            logLine("Error during cleanup: " . $cleanupError->getMessage(), 'ERROR');
        }
    }
    
    logLine("Resources cleaned up");
    logLine("=== Script finished ===");
}
?>