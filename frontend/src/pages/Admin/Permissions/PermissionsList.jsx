import { useEffect, useState } from "react";
import { getPermissions } from "../../../services/api";

export default function PermissionsList() {
  const [permissions, setPermissions] = useState([]);

  useEffect(() => {
    getPermissions().then(data => setPermissions(data.data || []));
  }, []);

  return (
    <div>
      <h1>قائمة الصلاحيات</h1>
      <table className="data-table">
        <thead><tr><th>#</th><th>اسم الصلاحية</th></tr></thead>
        <tbody>
          {permissions.map(perm => (
            <tr key={perm.id}><td>{perm.id}</td><td>{perm.name}</td></tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}