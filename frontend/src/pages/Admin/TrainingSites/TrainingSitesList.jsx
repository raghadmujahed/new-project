import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getTrainingSites, deleteTrainingSite } from "../../../services/api";
import BulkUploadTrainingSitesModal from "../../../components/admin/BulkUploadTrainingSitesModal";

export default function TrainingSitesList() {
  const [sites, setSites] = useState([]);
  const [modalOpen, setModalOpen] = useState(false);
  useEffect(() => { fetchSites(); }, []);
  const fetchSites = async () => { const data = await getTrainingSites(); setSites(data.data || []); };
  const handleDelete = async (id) => { if (confirm("حذف موقع التدريب؟")) { await deleteTrainingSite(id); fetchSites(); } };
  return (
    <div>
      <div className="page-header">
        <h1>مواقع التدريب</h1>
        <div>
          <Link to="/admin/training-sites/create" className="btn-primary">+ إضافة موقع</Link>
        </div>
      </div>
      <table className="data-table">
        <thead><tr><th>الاسم</th><th>الموقع</th><th>المديرية</th><th>النوع</th><th>السعة</th><th>إجراءات</th></tr></thead>
        <tbody>{sites.map(s => (
          <tr key={s.id}><td>{s.name}</td><td>{s.location}</td><td>{s.directorate_label}</td><td>{s.site_type_label}</td><td>{s.capacity}</td>
          <td><Link to={`/admin/training-sites/edit/${s.id}`} className="btn-sm">تعديل</Link><button onClick={() => handleDelete(s.id)} className="btn-sm danger">حذف</button></td></tr>
        ))}</tbody>
      </table>
      <BulkUploadTrainingSitesModal isOpen={modalOpen} onClose={() => setModalOpen(false)} onSuccess={fetchSites} />
    </div>
  );
}