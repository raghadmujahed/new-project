import { useEffect, useState } from "react";
import { getFeatureFlags, updateFeatureFlag } from "../../../services/api";

export default function FeatureFlagsList() {
  const [flags, setFlags] = useState([]);
  useEffect(() => { getFeatureFlags().then(data => setFlags(data)); }, []);
  const toggle = async (id, current) => { await updateFeatureFlag(id, !current); setFlags(flags.map(f => f.id === id ? {...f, is_open: !current} : f)); };
  return (
    <div>
      <h1>الميزات الديناميكية</h1>
      <table className="data-table">
        <thead><tr><th>الاسم</th><th>الحالة</th><th>إجراء</th></tr></thead>
        <tbody>{flags.map(flag => (
          <tr key={flag.id}><td>{flag.display_name}</td><td>{flag.is_open ? "مفتوحة" : "مغلقة"}</td>
          <td><button onClick={() => toggle(flag.id, flag.is_open)} className="btn-sm">{flag.is_open ? "إغلاق" : "فتح"}</button></td></tr>
        ))}</tbody>
      </table>
    </div>
  );
}