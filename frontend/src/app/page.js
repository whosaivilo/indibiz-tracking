export const instant = false;

export default async function Home() {
  // 1. Menarik data dari API Laravel ("Pelayan") yang tadi kita buat
  const res = await fetch('http://127.0.0.1:8000/api/working-orders', { 
    cache: 'no-store' // Agar datanya selalu fresh setiap di-refresh
  });
  
  // 2. Merubah bentuk JSON menjadi Array yang bisa dibaca Javascript
  const responseJson = await res.json();
  const workingOrders = responseJson.data.data || [];

  // 3. Menampilkan ke HTML (React JSX)
  return (
    <div className="p-8 font-sans bg-gray-50 min-h-screen text-black">
      <h1 className="text-3xl font-bold mb-6 text-blue-600">Dashboard Tracking Indibiz</h1>
      
      <div className="overflow-x-auto bg-white rounded-lg shadow">
        <table className="min-w-full text-left border-collapse">
          <thead>
            <tr className="bg-blue-600 text-white">
              <th className="py-3 px-4">Wonum</th>
              <th className="py-3 px-4">Nama Customer</th>
              <th className="py-3 px-4">Jenis Order</th>
              <th className="py-3 px-4">Paket</th>
              <th className="py-3 px-4">Alamat</th>
            </tr>
          </thead>
          <tbody>
            {workingOrders.length === 0 ? (
              <tr>
                <td colSpan="5" className="py-4 text-center">Data tidak ditemukan...</td>
              </tr>
            ) : (
              workingOrders.map((wo, index) => (
                <tr key={wo.id || index} className="border-b hover:bg-gray-100">
                  <td className="py-3 px-4 font-semibold">{wo.wonum}</td>
                  <td className="py-3 px-4">{wo.nama_cust}</td>
                  <td className="py-3 px-4">
                    <span className="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">
                      {wo.jenis_order}
                    </span>
                  </td>
                  <td className="py-3 px-4">{wo.paket}</td>
                  <td className="py-3 px-4 text-sm max-w-xs truncate">{wo.alamat}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}
