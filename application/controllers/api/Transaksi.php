<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaksi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('M_barang', 'barang');
		$this->load->model('M_customer','customer');
		$this->load->model('M_transaksi','transaksi');
		$this->load->model('M_detail_transaksi','detail_transaksi');
	}

	public function tes()
	{
		$Date = date("Y-m-d");
		echo date('Y-m-d', strtotime($Date. ' + 4 days'));

	}

	public function insertTransaksi()
	{
		$file_gambar = $this->input->post('gambar');
		$nama_gambar = $this->generateIdTransaksi() + 1;
		$upload_path = "./uploads/{$nama_gambar}.jpg";


		$insert_detail_transaksi = null;
		$insert_customer = true;

		if ($file_gambar == "" || $file_gambar == null ) {
			$result = array(
				'status' => 0,
				'message' => "gambar null",
			);
		}else {
			file_put_contents($upload_path, base64_decode($file_gambar));
			

			//data customer
			$nama_customer = $this->input->post('nama_customer');
			$no_telp = $this->input->post('no_telp');
			$alamat = $this->input->post('alamat');	

			$id_customer = $this->input->post("id_customer");

			if ($id_customer == "") {
				$arr_customer = array(
					'nama_customer' => $nama_customer,
					'no_telp' => $no_telp,
					'alamat' => $alamat
				);

				$insert_customer = $this->customer->insert($arr_customer);
			}

			//data transaksi
			$tanggal_masuk = date("Y-m-d");
			$tanggal_keluar = date("Y-m-d");
			$id_customer = ($this->input->post("id_customer") != "" ? $this->input->post("id_customer") : $this->generateIdCustomer());
			$id_pegawai = $this->input->post('id_pegawai');
			$id_status = 1;
			$gambar = $nama_gambar.".jpg";

			$arr_transaksi = array(
				'tanggal_masuk' => $tanggal_masuk,
				'tanggal_keluar' => $tanggal_keluar,
				'id_customer' => $id_customer,
				'id_pegawai' => $id_pegawai,
				'id_status' => $id_status,
				'gambar' => $gambar,
				'estimasi' => date('Y-m-d', strtotime($tanggal_masuk. ' + 4 days'))
			);
			$insert_transaksi = $this->transaksi->insert($arr_transaksi);

			//detail transaksi
			$id_transaksi = $this->generateIdTransaksi();
			$id_barang = $this->input->post('id_barang');
			$jumlah_barang = $this->input->post('jumlah_barang');

			for ($i = 0; $i < count($id_barang); $i++) {
				$harga_satuan = $this->getHargaBarang($id_barang[$i]);
				$harga_total = $jumlah_barang[$i] * $harga_satuan;

				$arr_detail_transaksi = array(
					'id_transaksi' => $id_transaksi,
					'id_barang' => $id_barang[$i],
					'jumlah_barang' => $jumlah_barang[$i],
					'harga_total' => $harga_total,
					'estimasi' => date('Y-m-d', strtotime($tanggal_masuk. ' + 4 days'))
				);
				$insert_detail_transaksi = $this->detail_transaksi->insert($arr_detail_transaksi);
			}

			if ($insert_customer) {
				if ($insert_transaksi) {
					if ($insert_detail_transaksi) {
						$result = array(
							'id_transaksi' => $id_transaksi,
							'status' => 1,
							'message' => "sukses",
						);
						require_once(APPPATH.'views/vendor/autoload.php');
						$options = array(
							'cluster' => 'ap1',
							'useTLS' => true
						);
						$pusher = new Pusher\Pusher(
							'fbe5e22f9f78edda72c3',
							'f8cc57c1d3dbcfc9525f',
							'1014149',
							$options
						);

						$data['message'] = 'sukses';
						$data['from'] = 'transaksi';
						$pusher->trigger('my-channel', 'my-event', $data);

					}else {
						$result = array(
							'id_transaksi' => 0,
							'status' => 0,
							'message' => "detail transaksi gagal",
						);
					}
				}else {
					$result = array(
						'id_transaksi' => 0,
						'status' => 0,
						'message' => "transaksi gagal",
					);
				}
			}else {
				$result = array(
					'id_transaksi' => 0,
					'status' => 0,
					'message' => "customer gagal",
				);
			}
		}

		echo json_encode($result);		
	}

	public function getHargaBarang($id)
	{
		$harga_barang = null;
		$data_barang = $this->barang->get($id);
		foreach ($data_barang as $value) {
			$harga_barang = $value->total_harga;
		}

		return $harga_barang;
	}

	public function generateIdCustomer()
	{
		$data_customer = $this->customer->get();
		$id_customer = null;
		
		foreach ($data_customer as $value) {
			$id_customer = $value->id_customer;	
		}

		if ($id_customer == "") {
			return $id_customer+1;
		}else {
			return $id_customer;
		}
	}

	public function generateEstimasi()
	{
		$tanggal = date("Y-m-d");
		$jumlah_transaksi = 0;
		$transaksi = $this->transaksi->getByDate($tanggal);
		
		foreach ($transaksi as $value) {
			$jumlah_transaksi++;
		}

		echo json_encode($jumlah_transaksi); 
	}

	public function generateIdTransaksi()
	{
		$data_transaksi = $this->transaksi->get();
		$id_transaksi = null;
		
		foreach ($data_transaksi as $value) {
			$id_transaksi = $value->id_transaksi;	
		}

		if ($id_transaksi == "") {
			return $id_transaksi+1;
		}else {
			return $id_transaksi;
		}
	}

	public function totalHargaTransaksi($id_transaksi)
	{
		$detail_transaksi = $this->transaksi->viewTransaksiDetailByIdTransaksi($id_transaksi);
		$total_harga = 0;

		foreach ($detail_transaksi as $value) {
			$total_harga += $value->harga_total;
		}

		return $total_harga;
	}

	public function getGambar($id_transaksi)
	{
		$transaksi = $this->transaksi->get($id_transaksi);
		$gambar = $transaksi[0]->gambar;
		return $gambar;
	}

	public function getEstimasi($id_transaksi)
	{
		$transaksi = $this->transaksi->get($id_transaksi);
		$estimasi = $transaksi[0]->estimasi;
		return $estimasi;
	}
	
	public function getTransaksiById($id_transaksi)
	{
		$query = $this->transaksi->viewTransaksiDetailByIdTransaksi($id_transaksi);
		if ($query) {
			$result = array(
				'status' => 1,
				'message' => "sukses",
				'total_harga' => $this->totalHargaTransaksi($id_transaksi),
				'gambar' => base_url('uploads/'.$this->getGambar($id_transaksi)),
				'estimasi' => $this->getEstimasi($id_transaksi),
				'detail_customer' => $this->transaksi->viewCustomerByIdTransaksi($id_transaksi),
				'detail_barang' => $query
			);
		}else{
			$result = array(
				'status' => 0,
				'message' => "gagal",
				'total_harga' => 0,
				'gambar' => null,
				'estimasi' => "",
				'detail_customer' => array(),
				'detail_barang' => array()
			);
		}
		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function getTransaksiByCustomerName($customer_name)
	{
		$query = $this->transaksi->viewTransaksiByName($customer_name);
		$daftar_barang = array();
		if ($query) {
			foreach ($query as $key => $value) {
				$id_transaksi = $value->id_transaksi;
				$nama_customer = $value->nama_customer;
				$tanggal_masuk = $value->tanggal_masuk;
				$status = $value->nama_status;

				$data = array(
					'id_transaksi' => $id_transaksi,
					'nama_customer' => $nama_customer,
					'tanggal_masuk' => $tanggal_masuk,
					'status' => $status
				);

				array_push($daftar_barang, $data);
			}

			$result = array(
				'status' => 1,
				'message' => "sukses",
				'daftar_barang' => $daftar_barang 
			);			
		}else {
			$result = array(
				'status' => 0,
				'message' => "gagal",
				'daftar_barang' => $daftar_barang 
			);
		}

		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function getDaftarBarangMasuk()
	{
		$query = $this->transaksi->viewBarangMasuk();
		$daftar_barang = array();
		if ($query) {
			foreach ($query as $key => $value) {
				$id_transaksi = $value->id_transaksi;
				$nama_customer = $value->nama_customer;
				$tanggal_masuk = $value->tanggal_masuk;
				$status = $value->nama_status;

				$data = array(
					'id_transaksi' => $id_transaksi,
					'nama_customer' => $nama_customer,
					'tanggal_masuk' => $tanggal_masuk,
					'status' => $status
				);

				array_push($daftar_barang, $data);
			}

			$result = array(
				'status' => 1,
				'message' => "sukses",
				'daftar_barang' => $daftar_barang 
			);			
		}else {
			$result = array(
				'status' => 0,
				'message' => "gagal",
				'daftar_barang' => $daftar_barang 
			);
		}

		header('Content-Type: application/json');
		echo json_encode($result);
	}

	public function updateStatusTransaksi()
	{
		$id_transaksi = $this->input->post('id_transaksi');
		$id_status = $this->input->post('id_status');
		
		$object = array("id_status" => $id_status+1);
		$result = $this->transaksi->update($id_transaksi,$object);

		if ($result) {
			$result = array(
				'status' => 1,
				'message' => "sukses"
			);			
		}else {
			$result = array(
				'status' => 0,
				'message' => "gagal"
			);			
		}

		header('Content-Type: application/json');
		echo json_encode($result);
	}
}

/* End of file Transaksi.php */
/* Location: ./application/controllers/api/Transaksi.php */