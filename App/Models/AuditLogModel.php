<?php
namespace App\Models;

use App\Core\Model;

class AuditLogModel extends Model
{
    /**
     * Log an action to the audit_log table.
     *
     * @param string            $tableName The affected table (e.g., 'user').
     * @param int               $recordId The ID of the affected record.
     * @param string            $actionType The action type (e.g., 'CREATE', 'UPDATE', 'DELETE').
     * @param array|string      $changes Details of changes (e.g., JSON-encoded old/new values).
     * @param array|string|null $metadata Additional context (e.g., IP, user agent).
     * @param int|null          $changedBy The ID of the user performing the action.
     * @param int|null          $branchId The ID of the associated branch.
     * @return void
     */
    public function logAction(
        string $tableName,
        int $recordId,
        string $actionType,
        $changes,
        $metadata = null,
        ?int $changedBy = null,
        ?int $branchId = null
    ): void {
        $sql = '
            INSERT INTO audit_log (
                table_name, record_id, action_type, changes, metadata, changed_by, branch_id, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ';
        self::$db->query($sql, [
            $tableName,
            $recordId,
            $actionType,
            is_array($changes) ? json_encode($changes) : $changes,
            is_array($metadata) ? json_encode($metadata) : $metadata,
            $changedBy,
            $branchId
        ]);
    }
}