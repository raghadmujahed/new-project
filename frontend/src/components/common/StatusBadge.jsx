const statusMap = {
  active: "badge-success",
  pending: "badge-warning",
  approved: "badge-primary",
};

export default function StatusBadge({ label, status = "pending" }) {
  const badgeClass = statusMap[status] || "badge-soft";

  return <span className={`badge-custom ${badgeClass}`}>{label}</span>;
}