<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transportasi extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if (empty($this->session->userdata('pengguna')))
		{
			redirect(base_url('pengguna/daftar'),'refresh');
		}
	}

	public function index()
	{
		$data['sub_judul'] = 'Daftar Transportasi';
		$data['pengguna'] = $this->transportasi_model->list();
		$this->template->pengguna('transportasi/semua', $data);
	}

	public function tambah()
	{
		if (aktif_sesi()['role'] == 'admin')
		{
			if ($this->input->method(TRUE) == 'POST')
			{
				$this->form_validation->set_rules('jenis', 'Jenis Kendaraan', 'trim|in_list[pickup,truck]|required');
				$this->form_validation->set_rules('nomor_plat', 'Nomor Plat', 'trim|is_unique[transportasi.nomor_plat]|required', array('is_unique' => 'Nomor plat sudah terdaftar'));
				$this->form_validation->set_rules('beban_angkut', 'Beban Angkut', 'trim|numeric|required');
				$this->form_validation->set_rules('sopir', 'Sopir', 'trim|required');
				
				if ($this->form_validation->run() == TRUE)
				{
					$this->transportasi_model->create(array(
						'jenis' => $this->input->post('jenis'),
						'nomor_plat' => $this->input->post('nomor_plat'),
						'beban_angkut' => $this->input->post('beban_angkut'),
						'pengemudi' => $this->input->post('sopir'),
						'status' => $this->input->post('status')
					));

					$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Transportasi berhasil ditambahkan'));
					redirect(base_url('transportasi') ,'refresh');
				}
				else
				{
					$data['sub_judul'] = 'Tambah Transportasi';
					$data['sopir'] = $this->pengguna_model->get_where(array('role' => 'sopir'));
					$this->template->pengguna('transportasi/tambah', $data);
				}
			}
			else
			{
				$data['sub_judul'] = 'Tambah Transportasi';
				$data['sopir'] = $this->pengguna_model->get_where(array('role' => 'sopir'));
				$this->template->pengguna('transportasi/tambah', $data);
			}
		}
		else
		{
			show_error('Anda tidak memiliki hak untuk melakukannya', 401 , 'Unauthorized action');
		}
	}

	public function sunting($transportasi_id = NULL)
	{
		if (aktif_sesi()['role'] == 'admin')
		{
			$transportasi = $this->transportasi_model->view($transportasi_id);

			if ($transportasi)
			{
				if ($this->input->method(TRUE) == 'POST')
				{
					$this->form_validation->set_rules('jenis', 'Jenis Kendaraan', 'trim|in_list[pickup,truck]|required');
					$this->form_validation->set_rules('nomor_plat', 'Nomor Plat', 'trim|required');
					$this->form_validation->set_rules('beban_angkut', 'Beban Angkut', 'trim|numeric|required');
					$this->form_validation->set_rules('sopir', 'Sopir', 'trim|required');

					if ($this->form_validation->run() == TRUE)
					{
						$this->transportasi_model->update(array(
							'jenis' => $this->input->post('jenis'),
							'nomor_plat' => $this->input->post('nomor_plat'),
							'beban_angkut' => $this->input->post('beban_angkut'),
							'pengemudi' => $this->input->post('sopir'),
							'status' => $this->input->post('status')
						), array('id' => $this->uri->segment(3)));

						$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Transportasi berhasil diperbaharui'));
						redirect(base_url('transportasi'), 'refresh');
					}
					else
					{
						$data['sub_judul'] = 'Sunting Transportasi';
						$data['transportasi'] = $transportasi;
						$data['sopir'] = $this->pengguna_model->get_where(array('role' => 'sopir'));
						$this->template->pengguna('transportasi/sunting', $data);
					}
				}
				else
				{
					$data['sub_judul'] = 'Sunting Transportasi';
					$data['transportasi'] = $transportasi;
					$data['sopir'] = $this->pengguna_model->get_where(array('role' => 'sopir'));
					$this->template->pengguna('transportasi/sunting', $data);
				}
			}
			else
			{
				show_404();
			}
		}
		else
		{
			show_error('Anda tidak memiliki hak untuk melakukannya', 401 , 'Unauthorized action');
		}
	}

	public function hapus($transportasi_id = NULL)
	{
		if (aktif_sesi()['role'] == 'admin')
		{
			$transportasi = $this->transportasi_model->delete(array('id' => $transportasi_id));

			if ($transportasi)
			{
				$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Transportasi berhasil dihapus'));
			}

			redirect(base_url('transportasi') ,'refresh');
		}
		else
		{
			show_error('Anda tidak memiliki hak untuk melakukannya', 401 , 'Unauthorized action');
		}
	}

	public function pesan($transportasi_id = NULL)
	{
		if (!empty($transportasi_id))
		{
			if ($this->input->method(TRUE) == 'POST')
			{
				$this->form_validation->set_rules('transportasi_id', 'Transportasi ID', 'trim|integer|required');
				$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
				$this->form_validation->set_rules('seluler', 'Kontak Yang Dapat Dihubungi', 'trim|numeric|max_length[15]|required');
				$this->form_validation->set_rules('jadwal_angkut', 'Jadwal Angkut', 'trim|required');
				$this->form_validation->set_rules('penjemputan', 'Penjemputan', 'trim|required');
				$this->form_validation->set_rules('beban_angkut', 'Penjemputan', 'trim|numeric');
				
				if ($this->form_validation->run() == TRUE)
				{
					$cek_pemesanan_sebelumnya = $this->pesanan_transportasi_model->get_where(array(
						'transportasi_id' => $this->input->post('transportasi_id'),
						'pemesan' => aktif_sesi()['id'],
						'status' => 'pesan'
					));

					if (empty($cek_pemesanan_sebelumnya))
					{
						$data = array(
							'transportasi_id' => $this->input->post('transportasi_id'),
							'pemesan' => aktif_sesi()['id'],
							'nama_lengkap' => $this->input->post('nama_lengkap'),
							'seluler' => $this->input->post('seluler'),
							'jadwal_angkut' => nice_date($this->input->post('jadwal_angkut'), 'Y-m-d H:i:s'),
							'penjemputan' => $this->input->post('penjemputan'),
							'tanggal_pemesanan' => nice_date(unix_to_human(now()), 'Y-m-d H:i:s'),
							'catatan' => $this->input->post('catatan'),
							'upah_angkut' =>  (!empty($this->input->post('beban_angkut')))?$this->setting_model->get_upah_angkut()['value']*$this->input->post('beban_angkut'):0,
							'status' => 'pesan'	
						);

						$this->pesanan_transportasi_model->create($data);

						$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Mohon tunggu, pesanan anda akan di konfirmasi oleh admin'));
					}
					else
					{
						$this->session->set_flashdata('flash_message', array('status' => 'warning', 'message' => 'Mohon maaf, anda sudah melakukan pemesanan untuk produk ini, mohon tunggu konfirmasi admin'));
					}

					redirect(base_url('transportasi/pesanan_saya') ,'refresh');
				}
				else
				{
					$data['sub_judul'] = 'Pesan Transportasi';
					$data['transportasi'] = $this->transportasi_model->get_where(array('status' => 'tersedia'));
					$this->template->pengguna('transportasi/pesan', $data);
				}
			}
			else
			{
				$data['sub_judul'] = 'Pesan Transportasi';
				$data['transportasi'] = $this->transportasi_model->get_where(array('status' => 'tersedia'));
				$this->template->pengguna('transportasi/pesan', $data);
			}
		}
		else
		{
			$data['sub_judul'] = 'Pesan Transportasi';
			$data['transportasi'] = $this->transportasi_model->get_where(array('status' => 'tersedia'));
			$this->template->pengguna('transportasi/daftar', $data);
		}
	}

	public function sunting_pesanan($pesanan_id = NULL)
	{
		if (!empty($pesanan_id))
		{
			if ($this->input->method(TRUE) == 'POST')
			{
				$this->form_validation->set_rules('transportasi_id', 'Transportasi ID', 'trim|integer|required');
				$this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap', 'trim|required');
				$this->form_validation->set_rules('seluler', 'Kontak Yang Dapat Dihubungi', 'trim|numeric|max_length[15]|required');
				$this->form_validation->set_rules('jadwal_angkut', 'Jadwal Angkut', 'trim|required');
				$this->form_validation->set_rules('penjemputan', 'Penjemputan', 'trim|required');
				$this->form_validation->set_rules('beban_angkut', 'Penjemputan', 'trim|numeric');
				
				if ($this->form_validation->run() == TRUE)
				{
					$pesanan = $this->pesanan_transportasi_model->view($pesanan_id);

					$penjualan = (!empty($this->input->post('beban_angkut')))?$this->setting_model->get_harga_sawit()['value']*$this->input->post('beban_angkut'):$pesanan['penjualan'];
					$upah_angkut = (!empty($this->input->post('beban_angkut')))?$this->setting_model->get_upah_angkut()['value']*$this->input->post('beban_angkut'):$pesanan['upah_angkut'];
					$total_pendapatan = (!empty($this->input->post('beban_angkut')))?($this->setting_model->get_harga_sawit()['value']*$this->input->post('beban_angkut'))-$upah_angkut:$pesanan['total_pendapatan'];

					$data = array(
						'transportasi_id' => $pesanan['transportasi_id'],
						'pemesan' => $pesanan['pemesan'],
						'nama_lengkap' => $this->input->post('nama_lengkap'),
						'seluler' => $this->input->post('seluler'),
						'jadwal_angkut' => nice_date($this->input->post('jadwal_angkut'), 'Y-m-d H:i:s'),
						'penjemputan' => $this->input->post('penjemputan'),
						'catatan' => $this->input->post('catatan'),
						'penjualan' => $penjualan,
						'upah_angkut' =>  $upah_angkut,
						'total_pendapatan' =>  $total_pendapatan,
						'status' => $pesanan['status']
					);

					$update = $this->pesanan_transportasi_model->update($data, array('id' => $pesanan_id));
					$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Pesanan berhasil diperbaharui'));

					redirect(base_url('transportasi/pesanan') ,'refresh');
				}
				else
				{
					$data['sub_judul'] = 'Sunting Pesanan';
					$data['pesanan_transportasi'] = $this->pesanan_transportasi_model->view($pesanan_id);
					$this->template->pengguna('transportasi/sunting_pesanan', $data);
				}
			}
			else
			{
				$data['sub_judul'] = 'Sunting Pesanan';
				$data['pesanan_transportasi'] = $this->pesanan_transportasi_model->view($pesanan_id);
				$this->template->pengguna('transportasi/sunting_pesanan', $data);
			}
		}
		else
		{
			show_404();
		}
	}

	public function pesanan()
	{
		$data['sub_judul'] = 'Pesanan Transportasi';
		$data['pesanan_transportasi'] = $this->pesanan_transportasi_model->list();
		$this->template->pengguna('transportasi/pesanan', $data);
	}

	public function ubah_status_pesanan($pesanan_transportasi_id = NULL, $status = '')
	{
		if (!empty($status) && in_array($status, ['batal', 'konfirmasi', 'proses', 'selesai']))
		{
			$pesanan_transportasi = $this->pesanan_transportasi_model->view($pesanan_transportasi_id);

			if (!empty($pesanan_transportasi))
			{
				$do_it = FALSE;

				switch ($status)
				{
					/**
					 * yang diizinkan mengubah status pesanan menjadi "batal" hanya admin dan pemesan
					 */
					case 'batal':
						if (aktif_sesi()['role'] == 'admin' OR aktif_sesi()['id'] == $pesanan_transportasi['pemesan'])
						{
							$do_it = TRUE;
							$this->session->set_flashdata('flash_message', array('status' => 'warning', 'message' => 'Pesanan atas nama <b>'.$pesanan_transportasi['nama_lengkap'].'</b> telah dibatalkan'));
						}
						else
						{
							show_error('Anda tidak memiliki hak untuk melakukannya', 401 , 'Unauthorized action');
						}
					break;

					/**
					 * yang diizinkan mengubah status pesanan menjadi "konfirmasi" hanya admin
					 */
					case 'konfirmasi':
						if (aktif_sesi()['role'] == 'admin')
						{
							$do_it = TRUE;
							$this->session->set_flashdata('flash_message', array('status' => 'primary', 'message' => 'Pesanan atas nama <b>'.$pesanan_transportasi['nama_lengkap'].'</b> telah dikonfirmasi'));
						}
						else
						{
							show_error('Anda tidak memiliki hak untuk melakukannya', 401 , 'Unauthorized action');
						}
					break;

					/**
					 * yang diizinkan mengubah status pesanan menjadi "proses" hanya admin dan sopir yang dipesan
					 */
					case 'proses':
						$transportasi = $this->transportasi_model->view($pesanan_transportasi['transportasi_id']);

						if (!empty($transportasi))
						{
							if (aktif_sesi()['role'] == 'sopir' && aktif_sesi()['id'] !== $transportasi['pengemudi'])
							{
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Anda bukan sopir kendaraan ini'));
							}

							if ($transportasi['status'] == 'tersedia')
							{
								$do_it = TRUE;
								$this->transportasi_model->update(array('status' => 'tidak-tersedia'), array('id' => $transportasi['id']));
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Pesanan sedang di proses, status transportasi diubah menjadi tidak tersedia'));
							}
							else
							{
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Silahkan hubungi admin karena status anda masih belum tesedia, kemungkinan ada pesanan yang belum diselesaikan'));
							}
						}
						else
						{
							show_404();
						}

					break;

					/**
					 * yang diizinkan mengubah status pesanan menjadi "selesai" hanya admin dan sopir yang dipesan
					 */
					case 'selesai':

						$transportasi = $this->transportasi_model->view($pesanan_transportasi['transportasi_id']);

						if (!empty($transportasi))
						{
							if (aktif_sesi()['role'] == 'sopir' && aktif_sesi()['id'] !== $transportasi['pengemudi'])
							{
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Anda bukan sopir kendaraan ini'));
							}

							if ($transportasi['status'] == 'tidak-tersedia')
							{
								$do_it = TRUE;
								$this->transportasi_model->update(array('status' => 'tersedia'), array('id' => $pesanan_transportasi['transportasi_id']));
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Pesanan sudah sampai, status transportasi diubah menjadi tersedia kembali'));
							}
							else
							{
								$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Silahkan hubungi admin karena status anda masih belum tesedia, kemungkinan ada pesanan yang belum diselesaikan'));
							}
						}
						else
						{
							show_404();
						}
						
					break;
					
					default:
						$this->session->set_flashdata('flash_message', array('status' => 'success', 'message' => 'Error'));
					break;
				}

				if ($do_it == TRUE)
				{
					$this->pesanan_transportasi_model->update(array('status' => $status), array('id' => $pesanan_transportasi_id));
				}
			}
			else
			{
				$this->session->set_flashdata('flash_message', array('status' => 'danger', 'message' => 'Pesanan tidak ditemukan'));
			}

			redirect($this->input->get('redirect_to'), 'refresh');
		}
		else
		{
			show_error('Request not match', 500 , 'Internal server error');
		}
	}

	public function pesanan_saya($pesanan_transportasi_id = NULL)
	{
		$data['sub_judul'] = 'Pesanan Saya';
		$data['pesanan_transportasi'] = $this->pesanan_transportasi_model->get_where(array('pemesan' => aktif_sesi()['id']));
		$this->template->pengguna('transportasi/pesanan_saya', $data);
	}
}

/* End of file Transportasi.php */
/* Location: ./application/controllers/Transportasi.php */