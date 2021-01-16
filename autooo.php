<?php
	$c = mysql_connect("localhost", "root", "");
	mysql_select_db("db_ta");

	if(isset($_POST["cek_sms_masuk"]) && $_POST["cek_sms_masuk"]==true)
	{
		$q = mysql_query("SELECT * FROM inbox WHERE Processed = 'false'");
		while($r = mysql_fetch_object($q)){
			
			//JIKA FORMAT SMS = REG ID JADWAL
			if(preg_match("/REG (.*?) JADWAL/i", $r->TextDecoded, $o))
			{
				if(!empty($o[1])){

					$qjadwal= mysql_query("SELECT * FROM jadwal_periksa WHERE id_pasien= '".$o[1]."' ORDER BY kd_jadwal_periksa DESC");
					
						$handphone 	= $r->SenderNumber;
						$isi_sms	= "";

					if(@mysql_num_rows($qJadwal)>0)
					{
						
						while ($dJadwal= mysql_fetch_object($qjadwal))
						{		
							$isi_sms = "Jadwal Periksa Anda Hari: $dJadwal->hari_praktek Tanggal: $dJadwal->tgl_cek_up".",";
						}
					
					}
					else{

						$isi_sms="Jadwal periksa anda cari tidak ada";
					}
					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),'$handphone','$isi_sms',sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			
			}


			//END
			//JIKA FORMAT SMS = REG FORMAT
			if(preg_match("/REG FORMAT/i", $r->TextDecoded, $o))
			{
				//print_r($o);

					$handphone 	= $r->SenderNumber;
					$isi_sms 	= "Informasi Format SMS : REG#ID Pasien#FORMAT, REG#ID Pasien#JADWAL,REG#ID#NOANTRIAN, FORMAT INFO DOKTER : REG#SPESIALIS, REG#ID DOKTER#JADOK";

					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,
	SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),'$handphone','$isi_sms',
	sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			





			//JIKA FORMAT SMS = REG ID NOANTRIAN
			if(preg_match("/REG (.*?) NOANTRIAN/i", $r->TextDecoded, $o))
			{
				if(!empty($o[1])){

					$qjadwal	= mysql_query("SELECT * FROM jadwal_periksa WHERE id_pasien = '".$o[1]."' ORDER BY kd_jadwal_periksa DESC");
					$dJadwal  	= mysql_fetch_object($qjadwal);


					$handphone 	= $r->SenderNumber;
					$isi_sms 	= "Nomor Antrian Anda : $dJadwal->noantrean";

					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,
	SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),'$handphone','$isi_sms',
	sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			}

			//JIKA FORMAT REG ID PASIEN BATAL
			if(preg_match("/REG (.*?) BATAL/i", $r->TextDecoded, $o))
			{
				if(!empty($o[1])){

					$qjadwal	= mysql_query("DELETE FROM jadwal_periksa WHERE id_pasien = '".$o[1]."' ORDER BY kd_jadwal_periksa DESC");
					$dJadwal  	= mysql_fetch_object($qjadwal);


					$handphone 	= $r->SenderNumber;
					$isi_sms 	= "Jadwal Anda Telah Dihapus";

					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,
	SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),'$handphone','$isi_sms',
	sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			}
						

						//JIKA FORMAT REG SPESIALIS
//echo 1;
			if(preg_match("/REG SPESIALIS (MATA|UMUM|ANAK|PENYAKIT DALAM|GIGI)/i", $r->TextDecoded, $o))
			{
				//print_r($o);

				if(!empty($o[1])){

					$qjadwal	= mysql_query("SELECT spesialis.*, dokter.* FROM spesialis JOIN dokter ON spesialis.kd_spesialis = dokter.kd_spesialis WHERE spesialis.nama_spesialis = '".$o[1]."'");
					
					$handphone 	= $r->SenderNumber;
						$isi_sms	= "";
						

					if(@mysql_num_rows($qjadwal)>0)
					{
						
						while($dJadwal  = mysql_fetch_object($qjadwal))
						{
							$isi_sms .= "Nama Dokter : $dJadwal->nama_dokter ID Dokter : $dJadwal->id_dokter" . ",";
						}
					}else{
						$isi_sms = "Informasi Dokter yang anda cari tidak ada";
					}

					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,
	SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),$handphone,'$isi_sms',
	sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			}




			//JIKA FORMAT SMS REG JADOK
			if(preg_match("/REG (.*?) JADOK/i", $r->TextDecoded, $o))
			{
				if(!empty($o[1])){

					$qjadwal	= mysql_query("SELECT * FROM jadwal_dokter WHERE id_dokter ='".$o[1]."' ORDER BY kd_jadwal DESC");
					
						$handphone = $r->SenderNumber;
						$isi_sms = "";

					if(@mysql_num_rows($qjadwal)>0)
					{
						

						while ($dJadwal = mysql_fetch_object($qjadwal)) 
						{
							$isi_sms.="Jadwal Dokter : $dJadwal->hari_praktek, Jam : $dJadwal->mulai s/d $dJadwal->selesai".",";
						}
					}else{

						$isi_sms ="Jadwal Dokter Tidak Ada";
					}
					
					$sms_kirim 	= mysql_query("insert into outbox (InsertIntoDB,SendingDateTime,DestinationNumber,TextDecoded,
	SendingTimeOut,DeliveryReport,CreatorID) values (sysdate(),sysdate(),'$handphone','$isi_sms',
	sysdate(),'yes','system')");

					if($sms_kirim)
					{
						mysql_query("UPDATE inbox SET Processed = 'true' WHERE ID = '".$r->ID."'");
					}
				}
			}

//			
		}
		
	}
?>