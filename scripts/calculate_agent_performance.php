<?php

/**
 * Agent Performance Report Generator
 * 
 * This script generates daily agent performance metrics and saves them to the database.
 * Scheduled to run daily at 1:00 AM via cron job.
 * 
 * Usage: php scripts/calculate_agent_performance.php
 */

// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            putenv(trim($key) . '=' . trim($value));
        }
    }
}

// Get DB configuration from environment
function getDbConfig($connection = 'DEFAULT') {
    if ($connection === 'METRICS') {
        // Main database for metrics (auso_domex_new)
        return [
            'host' => getenv('DB_HOST') ?: '127.0.0.1',
            'port' => getenv('DB_PORT') ?: 3306,
            'database' => getenv('DB_DATABASE') ?: 'auso_domex_new',
            'user' => getenv('DB_USERNAME') ?: 'root',
            'password' => getenv('DB_PASSWORD') ?: '',
        ];
    }
    // Default: AC/Old database for source data (auso_main_new)
    return [
        'host' => getenv('AC_DB_HOST') ?: '127.0.0.1',
        'port' => getenv('AC_DB_PORT') ?: 3306,
        'database' => getenv('AC_DB_DATABASE') ?: 'auso_main_new',
        'user' => getenv('AC_DB_USERNAME') ?: 'root',
        'password' => getenv('AC_DB_PASSWORD') ?: '',
    ];
}

// Create PDO connections
function createPDO($config) {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ]);
    return $pdo;
}

// Start of day (yesterday) when script runs at 1:00 AM
$yesterday = date('Y-m-d', strtotime('-1 day'));
$today = date('Y-m-d');

try {
    // Use AC database (auso_call_server) for source data
    $pdoAC = createPDO(getDbConfig('DEFAULT'));
    
    // Use metrics database (auso_domex_new) for additional metrics like active_time
    $pdoMetrics = createPDO(getDbConfig('METRICS'));

    echo "[" . date('Y-m-d H:i:s') . "] Starting Agent Performance Report Generation for: {$yesterday}\n";

    // Get all active agents from AC database
    $stmt = $pdoAC->prepare("
        SELECT id, username, extension 
        FROM au_user 
        WHERE usertype = 'Agent' 
        AND deleted_at IS NULL
        ORDER BY id
    ");
    $stmt->execute();
    $agents = $stmt->fetchAll();

    $processedCount = 0;
    $errorCount = 0;

    foreach ($agents as $agent) {
        try {
            $agentId = $agent->id;
            $agentName = $agent->username;
            $extension = $agent->extension ?? 'N/A';

            echo "Processing agent: {$agentName} (ID: {$agentId}) - Extension: {$extension}\n";

            // Calculate metrics for the given date
            $metrics = calculateAgentMetrics($pdoAC, $pdoMetrics, $agentId, $extension, $yesterday);

            // Prepare data for save
            $performanceData = [
                'date' => $yesterday,
                'agent_id' => $agentId,
                'agent_name' => $agentName,
                'extension' => $extension,
                'total_calls' => $metrics['total_calls'] ?? 0,
                'avg_calls' => $metrics['avg_calls'] ?? 0,
                'total_missed' => $metrics['total_missed'] ?? 0,
                'avg_missed' => $metrics['avg_missed'] ?? 0,
                'acw' => $metrics['acw'] ?? 0,
                'avg_acw' => $metrics['avg_acw'] ?? 0,
                'other_break' => $metrics['other_break'] ?? 0,
                'avg_oth_break' => $metrics['avg_oth_break'] ?? 0,
                'active_time' => $metrics['active_time'] ?? 0,
                'talk_time' => $metrics['talk_time'] ?? 0,
                'avg_talk_time' => $metrics['avg_talk_time'] ?? 0,
                'tickets_created_count' => $metrics['tickets_created_count'] ?? 0,
                'queues' => isset($metrics['queues']) ? json_encode($metrics['queues']) : null,
            ];

            // Check if record exists
            $checkStmt = $pdoAC->prepare("
                SELECT id FROM ac_agent_performance 
                WHERE date = ? AND agent_id = ?
            ");
            $checkStmt->execute([$yesterday, $agentId]);
            $existingRecord = $checkStmt->fetch();

            if ($existingRecord) {
                // Update existing record
                $updateStmt = $pdoAC->prepare("
                    UPDATE ac_agent_performance SET
                        agent_name = ?,
                        extension = ?,
                        total_calls = ?,
                        avg_calls = ?,
                        total_missed = ?,
                        avg_missed = ?,
                        acw = ?,
                        avg_acw = ?,
                        other_break = ?,
                        avg_oth_break = ?,
                        active_time = ?,
                        talk_time = ?,
                        avg_talk_time = ?,
                        tickets_created_count = ?,
                        queues = ?,
                        updated_at = NOW()
                    WHERE date = ? AND agent_id = ?
                ");
                $updateStmt->execute([
                    $performanceData['agent_name'],
                    $performanceData['extension'],
                    $performanceData['total_calls'],
                    $performanceData['avg_calls'],
                    $performanceData['total_missed'],
                    $performanceData['avg_missed'],
                    $performanceData['acw'],
                    $performanceData['avg_acw'],
                    $performanceData['other_break'],
                    $performanceData['avg_oth_break'],
                    $performanceData['active_time'],
                    $performanceData['talk_time'],
                    $performanceData['avg_talk_time'],
                    $performanceData['tickets_created_count'],
                    $performanceData['queues'],
                    $yesterday,
                    $agentId,
                ]);
            } else {
                // Insert new record
                $insertStmt = $pdoAC->prepare("
                    INSERT INTO ac_agent_performance (
                        date, agent_id, agent_name, extension, total_calls, avg_calls,
                        total_missed, avg_missed, acw, avg_acw, other_break, avg_oth_break,
                        active_time, talk_time, avg_talk_time, tickets_created_count, queues,
                        created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $insertStmt->execute([
                    $performanceData['date'],
                    $performanceData['agent_id'],
                    $performanceData['agent_name'],
                    $performanceData['extension'],
                    $performanceData['total_calls'],
                    $performanceData['avg_calls'],
                    $performanceData['total_missed'],
                    $performanceData['avg_missed'],
                    $performanceData['acw'],
                    $performanceData['avg_acw'],
                    $performanceData['other_break'],
                    $performanceData['avg_oth_break'],
                    $performanceData['active_time'],
                    $performanceData['talk_time'],
                    $performanceData['avg_talk_time'],
                    $performanceData['tickets_created_count'],
                    $performanceData['queues'],
                ]);
            }

            echo "  ✓ Performance record saved successfully\n";
            $processedCount++;

        } catch (Exception $e) {
            echo "  ✗ Error processing agent {$agentName}: {$e->getMessage()}\n";
            $errorCount++;
        }
    }

    echo "\n[" . date('Y-m-d H:i:s') . "] Agent Performance Report Generation Complete\n";
    echo "  Agents Processed: {$processedCount}\n";
    echo "  Errors: {$errorCount}\n";

} catch (Exception $e) {
    echo "[ERROR] " . date('Y-m-d H:i:s') . ": {$e->getMessage()}\n";
    exit(1);
}


/**
 * Calculate all performance metrics for an agent on a specific date
 * 
 * @param PDO $pdoAC AC database connection (auso_call_server)
 * @param PDO $pdoMetrics Metrics database connection (auso_domex_new)
 * @param int $agentId
 * @param string $extension Agent extension
 * @param string $date (Y-m-d format)
 * @return array
 */
function calculateAgentMetrics($pdoAC, $pdoMetrics, $agentId, $extension, $date)
{
    $startDate = $date . ' 00:00:00';
    $endDate = $date . ' 23:59:59';

    $metrics = [
        'total_calls' => 0,
        'avg_calls' => 0,
        'total_missed' => 0,
        'avg_missed' => 0,
        'acw' => 0,
        'avg_acw' => 0,
        'other_break' => 0,
        'avg_oth_break' => 0,
        'active_time' => 0,
        'talk_time' => 0,
        'avg_talk_time' => 0,
        'tickets_created_count' => 0,
        'queues' => [],
    ];

    try {
        // 1. Total calls and answered calls from callcount table
        $stmt = $pdoAC->prepare("
            SELECT 
                COUNT(*) as total_calls,
                SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as answered_calls,
                SUM(COALESCE(duration, 0)) as talk_time
            FROM callcount
            WHERE agent_id = ? AND DATE(date) = ?
        ");
        
        $stmt->execute([$agentId, $date]);
        $callMetrics = $stmt->fetch();

        if ($callMetrics && $callMetrics->total_calls > 0) {
            $answeredCalls = (int) ($callMetrics->answered_calls ?? 0);
            $totalCalls = (int) $callMetrics->total_calls;
            $missedCalls = $totalCalls - $answeredCalls;
            
            $metrics['total_calls'] = $answeredCalls;
            $metrics['total_missed'] = $missedCalls;
            $metrics['talk_time'] = (int) ($callMetrics->talk_time ?? 0);

            // Calculate averages as ratios
            $metrics['avg_calls'] = $totalCalls > 0 ? round($answeredCalls / $totalCalls, 2) : 0;
            $metrics['avg_missed'] = $totalCalls > 0 ? round($missedCalls / $totalCalls, 2) : 0;
            $metrics['avg_talk_time'] = $answeredCalls > 0 ? round($metrics['talk_time'] / $answeredCalls, 2) : 0;
        }

        // 3. Active time from ac_agent_logins table (auso_domex_new)
        // Calculate total time agent was logged in by summing login sessions
        $stmt = $pdoMetrics->prepare("
            SELECT 
                SUM(TIMESTAMPDIFF(SECOND, l.login_time, COALESCE(l.logout_time, NOW()))) as total_active_time
            FROM ac_agent_logins l
            JOIN ac_users u ON l.user_id = u.id
            WHERE u.extension = ? AND DATE(l.login_time) = ?
        ");
        $stmt->execute([$extension, $date]);
        $activeTime = $stmt->fetch();

        if ($activeTime && $activeTime->total_active_time) {
            $metrics['active_time'] = (int) $activeTime->total_active_time;
        }

        // 4. Break times from au_agentbreak_summery_report (ACW and other breaks)
        $stmt = $pdoAC->prepare("
            SELECT 
                `desc` as break_type,
                SUM(TIMESTAMPDIFF(SECOND, breaktime, unbreaktime)) as total_break_time,
                COUNT(*) as break_count
            FROM au_agentbreak_summery_report
            WHERE agentid = ? AND DATE(date) = ?
            GROUP BY `desc`
        ");
        $stmt->execute([$agentId, $date]);
        $breakMetrics = $stmt->fetchAll();

        $totalBreakTime = 0;
        $totalBreakCount = 0;
        $acwTime = 0;

        if ($breakMetrics) {
            foreach ($breakMetrics as $breakMetric) {
                if ($breakMetric->break_type === 'ACW') {
                    $metrics['acw'] = (int) ($breakMetric->total_break_time ?? 0);
                    $acwTime = (int) ($breakMetric->total_break_time ?? 0);
                    if ($breakMetric->break_count > 0) {
                        $metrics['avg_acw'] = round($metrics['acw'] / $breakMetric->break_count, 2);
                    }
                } else {
                    // Other breaks (lunch, tea, etc)
                    $totalBreakTime += (int) ($breakMetric->total_break_time ?? 0);
                    $totalBreakCount += (int) $breakMetric->break_count;
                }
            }
        }

        if ($totalBreakCount > 0) {
            $metrics['other_break'] = $totalBreakTime;
            $metrics['avg_oth_break'] = round($totalBreakTime / $totalBreakCount, 2);
        }

        // 5. Queue metrics from au_queuecount_report
        $stmt = $pdoAC->prepare("
            SELECT 
                queuename,
                COUNT(*) as queue_call_count,
                SUM(COALESCE(COUNT(*), 0)) OVER (PARTITION BY queuename) as total_in_queue
            FROM au_queuecount_report
            WHERE agent = ? AND DATE(date) = ?
            GROUP BY queuename
        ");
        $stmt->execute([$extension, $date]);
        $queueRows = $stmt->fetchAll();

        $queues = [];
        if ($queueRows) {
            foreach ($queueRows as $row) {
                $queues[] = [
                    'name' => $row->queuename,
                    'call_count' => (int) $row->queue_call_count
                ];
            }
        }
        $metrics['queues'] = $queues;

        // 6. Tickets created count - note: ticket table not found in current database
        // This would require querying from another database or external system
        $metrics['tickets_created_count'] = 0;

    } catch (Exception $e) {
        echo "    Warning - Error calculating metrics: {$e->getMessage()}\n";
        // Return default metrics on error
    }

    return $metrics;
}
