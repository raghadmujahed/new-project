import { useEffect, useState } from "react";
import { getActivityLogs, getUsers } from "../../../services/api";

export default function ActivityLogsList() {
  const [logs, setLogs] = useState([]);
  const [users, setUsers] = useState([]);
  const [filters, setFilters] = useState({ user_id: "", action: "" });
  const [loading, setLoading] = useState(false);

  const fetchUsers = async () => {
    try {
      const data = await getUsers({ per_page: 100 });
      setUsers(data.data || []);
    } catch (err) {
      console.error("فشل تحميل المستخدمين", err);
    }
  };

  const fetchLogs = async () => {
    setLoading(true);
    try {
      const data = await getActivityLogs(filters);
      setLogs(data.data || []);
    } catch (err) {
      console.error("فشل تحميل سجل النشاطات", err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchUsers();
  }, []);

  useEffect(() => {
    fetchLogs();
  }, [filters]);

  return (
    <div>
      <h1>سجل النشاطات</h1>

      {/* الفلاتر */}
      <div className="filters-bar">
        <select
          value={filters.user_id}
          onChange={(e) =>
            setFilters({ ...filters, user_id: e.target.value })
          }
        >
          <option value="">كل المستخدمين</option>
          {users.map((user) => (
            <option key={user.id} value={user.id}>
              {user.name}
            </option>
          ))}
        </select>

        <input
          type="text"
          placeholder="الإجراء (مثال: login)"
          value={filters.action}
          onChange={(e) =>
            setFilters({ ...filters, action: e.target.value })
          }
        />
      </div>

      {/* الجدول */}
      {loading ? (
        <div>جاري التحميل...</div>
      ) : (
        <table className="data-table">
          <thead>
            <tr>
              <th>المستخدم</th>
              <th>الحدث</th>
              <th>الوصف</th>
              <th>IP</th>
              <th>التاريخ</th>
            </tr>
          </thead>

          <tbody>
            {logs.map((log) => {
              // استخراج البيانات من properties
              const props =
                typeof log.properties === "string"
                  ? JSON.parse(log.properties)
                  : log.properties || {};

              return (
                <tr key={log.id}>
                  <td>{log.causer?.name || "—"}</td>

                  {/* event أو log_name */}
                  <td>{log.event || log.log_name || "—"}</td>

                  <td>{log.description}</td>

                  {/* IP داخل properties */}
                  <td>{props.ip || "—"}</td>

                  <td>
                    {new Date(log.created_at).toLocaleString()}
                  </td>
                </tr>
              );
            })}

            {logs.length === 0 && (
              <tr>
                <td colSpan="5">لا توجد سجلات</td>
              </tr>
            )}
          </tbody>
        </table>
      )}
    </div>
  );
}