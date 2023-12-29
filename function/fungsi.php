<?php
set_time_limit(0);

function KonDecRomawi($angka)
{	$hsl = "";
    if($angka<1||$angka>3999){
        $hsl = "Batas Angka 1 s/d 3999";
    }else{
         while($angka>=1000){
             $hsl .= "M";
             $angka -= 1000;
         }
         if($angka>=500){
             if($angka>500){
                 if($angka>=900){
                     $hsl .= "M";
                     $angka-=900;
                 }else{
                     $hsl .= "D";
                     $angka-=500;
                 }
             }
         }
         while($angka>=100){
             if($angka>=400){
                 $hsl .= "CD";
                 $angka-=400;
             }else{
                 $angka-=100;
             }
         }
         if($angka>=50){
             if($angka>=90){
                 $hsl .= "XC";
                  $angka-=90;
             }else{
                $hsl .= "L";
                $angka-=50;
             }
         }
         while($angka>=10){
             if($angka>=40){
                $hsl .= "XL";
                $angka-=40;
             }else{
                $hsl .= "X";
                $angka-=10;
             }
         }
         if($angka>=5){
             if($angka==9){
                 $hsl .= "IX";
                 $angka-=9;
             }else{
                $hsl .= "V";
                $angka-=5;
             }
         }
         if ($angka==4) { $hsl .= "IV"; }
         while($angka>=1 AND $angka<=3){
             if($angka==4){
                $hsl .= "IV";
                $angka-=4;
             }else{
    }
                $hsl .= "I";
                $angka-=1;
             }
         }
    return ($hsl);
};

function buat_halaman($jumData,$dataPerPage,$noPage,$showPage,$url)
{	// menentukan jumlah halaman yang muncul berdasarkan jumlah semua data
	$jumPage = ceil($jumData/$dataPerPage);
	// menampilkan link previous
	$pageprev = $noPage-1;
	if ($noPage > 1) echo  "<a href='$url&page=$pageprev'>&lt;&lt; Prev</a>";
	// memunculkan nomor halaman dan linknya
	for($page = 1; $page <= $jumPage; $page++)
	{	if ((($page >= $noPage - 3) && ($page <= $noPage + 3)) || ($page == 1) || ($page == $jumPage))
		{	if (($showPage == 1) && ($page != 2))  echo "...";
			if (($showPage != ($jumPage - 1)) && ($page == $jumPage))  echo "...";
			if ($page == $noPage) echo "[".$page."]";
			else echo " <a href='$url&page=$page'>$page</a>";
			$showPage = $page;
		}
	}
	// menampilkan link next
	$pagenext = $noPage+1;
	if ($noPage < $jumPage) echo "<a href='$url&page=$pagenext'> Next &gt;&gt;</a>";

};

function hitung_bulan(\DateTime $date1, \DateTime $date2)
{
	$diff =  $date1->diff($date2);
	$months = $diff->y * 12 + $diff->m + $diff->d / 30;
	return (int) round($months);
};
	
function msgbox ($pesannya)
{	echo "<script language=\"Javascript\">\n";
	echo "window.alert('$pesannya');";
	echo "</script>";
};

function IsiCombo($qry, $def_val, $placeholder) 
{	if ($qry!="") 
	{ 	$sql = mysql_query($qry);
		if (!$sql) { die($qry. mysql_error()); }
		if ($placeholder!="")
		{echo "<option value='' disabled selected>$placeholder</option>";}
		while($data=mysql_fetch_array($sql))
		{	$isi_fld = $data['isi'];
			if ($def_val==$isi_fld)
				{echo "<option value='$data[isi]' selected='$data[isi]'>$data[tampil]</option>";}
			else
				{echo "<option value='$data[isi]'>$data[tampil]</option>";}
		}
	}
};		

function IsiComboN($qry, $def_val, $placeholder) 
{	if ($qry!="") 
	{ 	$sql = mysql_query($qry);
		if (!$sql) { die($qry. mysql_error()); }
		if ($placeholder!="")
		{echo "<option value='' disabled selected>$placeholder</option>";}
		while($data=mysql_fetch_array($sql))
		{	$isi_fld = $data['isi'];
			if ($def_val==$isi_fld)
				{echo "<option value='$data[isi]' selected='$data[isi]'>$data[tampil]</option>";}
			else
				{echo "<option value='$data[isi]'>$data[tampil]</option>";}
		}
	}
};

function tampil_data($qry,$jml_kolom) 
{ if ($qry!="")
  { $query = mysql_query($qry);
	if (!$query) { die($qry. mysql_error()); }
	$jumrowdata=mysql_num_rows($query);
	$no = 1; 
	while($data = mysql_fetch_array($query))
	{ 
echo "<tr>";
	  echo "<th scope='row'>$no</th>";
	  for ($i = 0; $i < $jml_kolom; $i++)
	  {	if (is_numeric($data[$i]))
	  	{	echo "<td align='right'>$data[$i]</td>"; }
	  	else
	  	{	echo "<td>$data[$i]</td>"; }
		}
	  echo "</tr>";
	  $no++;
    }
  }
};

function tampil_data_new($qry,$jml_kolom) 
{ if ($qry!="")
  { $query = mysql_query($qry);
	if (!$query) { die($qry. mysql_error()); }
	$jumrowdata=mysql_num_rows($query);
	$no = 1; 
	while($data = mysql_fetch_array($query))
	{ echo "<tr>";
	  echo "<th scope='row'>$no</th>";
	  for ($i = 0; $i < $jml_kolom; $i++)
	  {	if (is_numeric($data[$i]))
	  	{	echo "<td align='right'>$data[$i]</td>"; }
	  	else
	  	{	echo "<td>$data[$i]</td>"; }
		}
	  echo "</tr>";
	  $no++;
    }
  }
};

function tampil_data_tanpa_nourut($qry,$jml_kolom) 
{ if ($qry!="")
  { $query = mysql_query($qry);
	if (!$query) { die($qry. mysql_error()); }
	$jumrowdata=mysql_num_rows($query);
	$no = 1; 
	while($data = mysql_fetch_array($query))
	{ echo "<tr>";
	  for ($i = 0; $i < $jml_kolom; $i++)
	  {	if (is_numeric($data[$i]))
	  	{	echo "<td align='right'>$data[$i]</td>"; }
	  	else
	  	{	echo "<td>$data[$i]</td>"; }
		}
	  echo "</tr>";
	  $no++;
    }
  }
};

function tampil_data_print($qry,$jml_kolom,$jml_baris) 
{	$sql=mysql_query($qry);
	$jumrowdata=mysql_num_rows($sql);
	$barke = 1;
	while($data=mysql_fetch_array($sql))
	{ 	echo "<tr>";
			for ($i = 0; $i < $jml_kolom; $i++)
			{	if ($i==0) 
					{ echo "<td style='text-align: center;'>$barke</td>"; }
				else
					{ echo "<td style='text-align: left;'>$data[$i]</td>"; }
			}
		echo "</tr>";
		$barke = $barke + 1;
	}
	for ($i = $barke; $i < $jml_baris; $i++)
	{	echo "<tr border='0'>"; 
			for ($ii = 0; $ii < $jml_kolom; $ii++)
			{ echo "<td>&nbsp</td>"; }
		echo "</tr>";
	}
};

function tampil_data_chk($qry,$jml_kolom,$buat_link) 
{
	$sql=mysql_query($qry);
	while($data=mysql_fetch_array($sql))
	{ 
		echo "<tr>";
			echo "<td><input type='checkbox' name='chknik[]' value='$data[0]'></td>";
			for ($i = 0; $i < $jml_kolom; $i++)
			{
				if ($i==0 AND $buat_link!="") 
					{ echo "<td style='text-align: left;'><a href='$buat_link&cri=$data[$i]'>$data[$i]</a></td>"; }
				else
					{ echo "<td style='text-align: left;'>$data[$i]</td>"; }
			}
		echo "</tr>";
			
	}
};

function MiddleDate($date)
{
	$date= date("d-M-Y",strtotime($date));
	return "$date";
};

function SimpleDate($date)
{
	$date= date("m/d/Y",strtotime($date));
	return "$date";
};

function cek_masterstyle($id_so_det)
{	if($id_so_det!="")
	{	$cek=flookup("id_item","masterstyle","id_so_det='$id_so_det'");
		if($cek=="" or $cek==null)
		{	$sql="insert into masterstyle 
				(Styleno,Buyerno,DelDate,unit,itemname,Color,Size,id_so_det,KPNo,country,goods_code)
				select Styleno,so.Buyerno,DelDate_det,sod.unit,product_item,Color,Size,sod.id,KPNo,sod.dest,product_group from 
				so_det sod inner join so on sod.id_so=so.id 
				inner join act_costing ac on ac.id=so.id_cost
				inner join masterproduct mp on ac.id_product=mp.id 
				where sod.cancel='N' and sod.id='$id_so_det'";
			insert_log($sql,'Function');
		}
		$cek=flookup("id_item","masterstyle","id_so_det='$id_so_det'");
		return $cek;
	}
};

function flookup($fld,$tbl,$criteria)
{	if ($fld!="" AND $tbl!="" AND $criteria!="")
	{	$quenya = "Select $fld as namafld from $tbl Where $criteria ";
		$strsql = mysql_query($quenya);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		if (mysql_num_rows($strsql)=='0')
		{$hasil="";}
		else
		{$hasil=$rs['namafld'];}
		return $hasil;
	}
};

function flookup_new($conn,$fld,$tbl,$criteria)
{	if ($fld!="" AND $tbl!="" AND $criteria!="")
	{	$quenya = "Select $fld as namafld from $tbl Where $criteria ";
		$strsql = mysql_query($quenya);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		if (mysql_num_rows($strsql)=='0')
		{$hasil="";}
		else
		{$hasil=$rs['namafld'];}
		return $hasil;
	}
};

function get_stock_tgl($Pro,$Mat_Type,$Id_Item,$Tgl,$exc_bppb)
{	If ($Mat_Type == "FG")
	{	$cbomat = $Mat_Type;
		#$exc_bppb="SJ-FG14645";
		if ($Pro=="BPB")
		{	$maxbppbdate=flookup("max(bppbdate)","bppb","id_item='$Id_Item' and bppbno like 'SJ-FG%' 
				and bppbdate>='$Tgl' ");
			if ($Tgl>$maxbppbdate AND $maxbppbdate!="")
			{	$sql="select 0 tot_bpb,0 tot_bpb_nett,unit from bpb 
					where id_item='X' ";
			}
			else
			{	$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where 
					bpbno like 'FG%' and id_item='$Id_Item' and bpbdate<='$Tgl' 
					group by id_item";
			}
		}
		else
		{	if ($Pro=="BPB" AND $exc_bppb!="") { $sql_exc=" and bpbno!='$exc_bppb' "; } else { $sql_exc=""; } 
			$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where 
				bpbno like 'FG%' and id_item='$Id_Item' and bpbdate<='$Tgl' $sql_exc
				group by id_item";
		}
		$strsql = mysql_query($sql);
		if (!$strsql) { die($sql. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		
		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where 
			bppbno like 'SJ-FG%' and bppbno!='$exc_bppb' and id_item='$Id_Item' and bppbdate<='$Tgl' group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($sql. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		if (($tot_bppb==0 OR $tot_bppb==null) AND $Pro=="BPB")
		{ $stock = 0; }
		else
		{ $stock = round(($tot_bpb - $tot_bppb),2); }
	}
	Else
	{	$cbomat = $Mat_Type;
		if ($Pro=="BPB")
		{	$maxbppbdate=flookup("max(bppbdate)","bppb","id_item='$Id_Item' and bppbno like 'SJ-$cbomat%' 
				and bppbdate>='$Tgl' ");
			if ($Tgl>$maxbppbdate AND $maxbppbdate!="")
			{	$sql="select 0 tot_bpb,0 tot_bpb_nett,unit from bpb 
					where id_item='X' ";
			}
			else
			{	if ($Pro=="BPB" AND $exc_bppb!="") { $sql_exc=" and bpbno!='$exc_bppb' "; } else { $sql_exc=""; } 
				$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno not like 'FG%' and bpbno like '$cbomat%' and id_item='$Id_Item' 
					and bpbdate<='$Tgl' $sql_exc group by id_item";
			}
		}
		else
		{	if ($Pro=="BPB" AND $exc_bppb!="") { $sql_exc=" and bpbno!='$exc_bppb' "; } else { $sql_exc=""; } 
			$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno not like 'FG%' and bpbno like '$cbomat%' and id_item='$Id_Item' 
				and bpbdate<='$Tgl' $sql_exc group by id_item";
		}
		$strsql = mysql_query($sql);
		if (!$strsql) { die($sql. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		
		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where bppbno not like 'SJ-FG%' and bppbno like 'SJ-$cbomat%' and id_item='$Id_Item' 
			and (bppbdate<='$Tgl' OR bppbdate<>'$Tgl') group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($sql." ".mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		if (($tot_bppb==0 OR $tot_bppb==null) AND $Pro=="BPB")
		{ $stock = 0; }
		else
		{ $stock = round(($tot_bpb - $tot_bppb),2); }
	}
	return $stock;	
};

function fd($date)
{	if($date!="")
	{$format_tgl_sql = date('Y-m-d',strtotime($date));}
	else
	{$format_tgl_sql = "";}
	return $format_tgl_sql;
};

function fd_view($date)
{	if ($date=="0000-00-00" or $date=="0000-00-00 00:00:00" or $date==null)
	{	$format_tgl_sql = "";	}
	else
	{	$format_tgl_sql = date('d M Y',strtotime($date));	}
	return $format_tgl_sql;
};

function fd_view_dt($date)
{	if ($date=="0000-00-00 00:00:00" or $date==null)
	{	$format_tgl_sql = "";	}
	else
	{	$format_tgl_sql = date('d M Y H:m:s',strtotime($date));	}
	return $format_tgl_sql;
};

function urutkan($pro,$cbomat)
{	if ($pro=="Add_PO") { $cbomat2=$cbomat; $cbomat=substr($cbomat2,0,4); }
	if ($pro=="Add_BPPB" OR $pro=="Add_BPPB_FG" OR $pro=="Up_Out")
		{$hasil = flookup("bpbno","tempbpb","mattype='O.$cbomat'") + 1;}
	else if ($pro=="Add_Req")
		{$hasil = flookup("bpbno","tempbpb","mattype='R.$cbomat'") + 1;}
	else if ($pro=="Add_BPPBReq")
		{$hasil = flookup("bpbno","tempbpb","mattype='R.$cbomat'") + 1;}
	else if ($pro=="Add_RI")
		{$hasil = flookup("bpbno","tempbpb","mattype='RI.$cbomat'") + 1;}
	else if ($pro=="Add_RO")
		{$hasil = flookup("bpbno","tempbpb","mattype='RO.$cbomat'") + 1;}
	else
		{$hasil = flookup("bpbno","tempbpb","mattype='$cbomat'") + 1;}
	if ($hasil=='1')
		if ($pro=="OMat" OR $pro=="OFG" OR $pro=="Add_BPPB" OR $pro=="Add_BPPB_FG" OR $pro=="Up_Out")
			{mysql_query ("insert into tempbpb (mattype,bpbno) values ('O.$cbomat','1')");}
		else if ($pro=="Add_BPPBReq" OR $pro=="Add_Req")
			{mysql_query ("insert into tempbpb (mattype,bpbno) values ('R.$cbomat','1')");}
		else if ($pro=="Add_RI")
			{mysql_query ("insert into tempbpb (mattype,bpbno) values ('RI.$cbomat','1')");}
		else if ($pro=="Add_RO")
			{mysql_query ("insert into tempbpb (mattype,bpbno) values ('RO.$cbomat','1')");}
		else
			{mysql_query ("insert into tempbpb (mattype,bpbno) values ('$cbomat','1')");}
	else
		if ($pro=="OMat" OR $pro=="OFG" OR $pro=="Add_BPPB" OR $pro=="Add_BPPB_FG" OR $pro=="Up_Out")
			{mysql_query ("update tempbpb set bpbno='$hasil' where mattype='O.$cbomat'");}
		else if ($pro=="Add_BPPBReq" OR $pro=="Add_Req")
			{mysql_query ("update tempbpb set bpbno='$hasil' where mattype='R.$cbomat'");}
		else if ($pro=="Add_RI")
			{mysql_query ("update tempbpb set bpbno='$hasil' where mattype='RI.$cbomat'");}
		else if ($pro=="Add_RO")
			{mysql_query ("update tempbpb set bpbno='$hasil' where mattype='RO.$cbomat'");}
		else
			{mysql_query ("update tempbpb set bpbno='$hasil' where mattype='$cbomat'");}
	if ($pro=="OMat" OR $pro=="OFG" OR $pro=="Add_BPPB" OR $pro=="Add_BPPB_FG" OR $pro=="Up_Out")
		{ $hasil = trim('SJ-'.$cbomat.sprintf("%'.05d\n", $hasil));}
	else if ($pro=="Add_BPPBReq" OR $pro=="Add_Req")
		{ $hasil = trim('RQ-'.$cbomat.sprintf("%'.05d\n", $hasil));}
	else if ($pro=="Add_RI")
		{ $hasil = trim($cbomat.sprintf("%'.05d\n", $hasil)).'-R'; }
	else if ($pro=="Add_RO")
		{ $hasil = trim('SJ-'.$cbomat.sprintf("%'.05d\n", $hasil)).'-R';}
	elseif ($pro=="Add_PO")
		{ $hasil = trim('E/'.sprintf("%'.03d", $hasil).'/BM/'.KonDecRomawi(substr($cbomat2,5,2)).'/'.substr($cbomat,0,4)); }
	else
		{ $hasil = trim($cbomat.sprintf("%'.05d\n", $hasil));}
	return $hasil;
};

function urutkan_inq_local($cri1,$cri2)
{	$hasil = flookup("bpbno","tempbpb","mattype='$cri1'") + 1;
	if ($hasil=='1')
	{	$sql="insert into tempbpb (mattype,bpbno) values ('$cri1','1')";
		insert_log($sql,'');
	}
	else
	{	$sql="update tempbpb set bpbno='$hasil' where mattype='$cri1'";
		insert_log($sql,'');
	}
	$nm_company=flookup("company","mastercompany","company!=''");
	if(substr($cri2,0,13)=="LOC/EXIM-NAG/")
	{$hasil = trim(sprintf("%'.03d\n", $hasil))."/".trim($cri2);}
	else if($nm_company=="PT. Cheong Woon Indonesia")
	{$hasil = trim(substr($cri2,0,3).sprintf("%'.05d\n", $hasil).substr($cri2,3,8));
	 $hasil = preg_replace("/[\n\r]/","",$hasil);
	}
	else
	{$hasil = trim($cri2."/".sprintf("%'.05d\n", $hasil));}
	return $hasil;
};


function urutkan_inq($cri1,$cri2)
{	$hasil = flookup("bpbno","tempbpb","mattype='$cri1'") + 1;
	if ($hasil=='1')
	{	$sql="insert into tempbpb (mattype,bpbno) values ('$cri1','1')";
		insert_log($sql,'');
	}
	else
	{	$sql="update tempbpb set bpbno='$hasil' where mattype='$cri1'";
		insert_log($sql,'');
	}
	$nm_company=flookup("company","mastercompany","company!=''");
	if(substr($cri2,0,13)=="EXP/EXIM-NAG/")
	{$hasil = trim(sprintf("%'.03d\n", $hasil))."/".trim($cri2);}
	else if($nm_company=="PT. Cheong Woon Indonesia")
	{$hasil = trim(substr($cri2,0,3).sprintf("%'.05d\n", $hasil).substr($cri2,3,8));
	 $hasil = preg_replace("/[\n\r]/","",$hasil);
	}
	else
	{$hasil = trim($cri2."/".sprintf("%'.05d\n", $hasil));}
	return $hasil;
};

function urutkan_ws($cri1,$cri2)
{	$hasil = flookup("bpbno","tempbpb","mattype='$cri1'") + 1;
	if ($hasil=='1')
	{	$sql="insert into tempbpb (mattype,bpbno) values ('$cri1','1')";
		insert_log($sql,'');
	}
	else
	{	$sql="update tempbpb set bpbno='$hasil' where mattype='$cri1'";
		insert_log($sql,'');
	}
	$nm_company=flookup("company","mastercompany","company!=''");
	if(substr($cri2,0,13)=="EXP/EXIM-NAG/")
	{$hasil = trim(sprintf("%'.03d\n", $hasil))."/".trim($cri2);}
	else if($nm_company=="PT. Cheong Woon Indonesia")
	{$hasil = trim(substr($cri2,0,3).sprintf("%'.05d\n", $hasil).substr($cri2,3,8));
	 $hasil = preg_replace("/[\n\r]/","",$hasil);
	}
	else
	{$hasil = trim($cri2."/".sprintf("%'.03d\n", $hasil));}
	return $hasil;
};

function FT($time_input)
{
	$formatted = date('H:i:s',strtotime($time_input));
	return $formatted;
};

function insert_log($Querynya, $Trans_User)
{	if (!empty($_SERVER["HTTP_CLIENT_IP"]))
	{	$ip = $_SERVER["HTTP_CLIENT_IP"]; }
	elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
	else
	{ $ip = $_SERVER["REMOTE_ADDR"]; }
	$host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$Trans_Date = date("Y-m-d H:i:s");
	$Trans_Host = $Trans_User.'/'.$ip.'/'.$host_name;
	if (substr($Querynya,0,1)!="X")
	{	$strsql=mysql_query($Querynya);
		if (!$strsql) { die($Querynya. mysql_error()); }
	}
	if (substr($Querynya,0,1)!="X")
	{$QuerynyaRep = str_replace("'", "!",$Querynya);}
	else
	{$QuerynyaRep = str_replace("'", "!",substr($Querynya,1,strlen($Querynya)));}
	$que = "insert into act_hist (Trans_Date,Trans_Desc,Trans_Host) values ('$Trans_Date','$QuerynyaRep','$Trans_Host')";
	$strsql=mysql_query($que);
	if (!$strsql) { die($que. mysql_error()); }
};

function insert_log_new($conn,$Querynya, $Trans_User)
{	if (!empty($_SERVER["HTTP_CLIENT_IP"]))
	{	$ip = $_SERVER["HTTP_CLIENT_IP"]; }
	elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
	{ $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
	else
	{ $ip = $_SERVER["REMOTE_ADDR"]; }
	$host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']);
	$Trans_Date = date("Y-m-d H:i:s");
	$Trans_Host = $Trans_User.'/'.$ip.'/'.$host_name;
	if (substr($Querynya,0,1)!="X")
	{	$strsql=mysql_query($Querynya);
		if (!$strsql) { die($Querynya. mysql_error()); }
	}
	if (substr($Querynya,0,1)!="X")
	{$QuerynyaRep = str_replace("'", "!",$Querynya);}
	else
	{$QuerynyaRep = str_replace("'", "!",substr($Querynya,1,strlen($Querynya)));}
	$que = "insert into act_hist (Trans_Date,Trans_Desc,Trans_Host) values ('$Trans_Date','$QuerynyaRep','$Trans_Host')";
	$strsql=mysql_query($que);
	if (!$strsql) { die($que. mysql_error()); }
};

function nz($valnya)
{	if ($valnya=="")
	{	$hasil=0;	}
	else
	{	$hasil=$valnya;	}
};

function nb($valnya)
{	if ($valnya=="")
	{$hasil="";}
	else if ($valnya==null)
	{$hasil="";}
	else
	{$hasil=trim(strtoupper(str_replace("'","",$valnya)));}
	return $hasil;
};

function menu_ua($valnya)
{	if ($valnya=="") {$hasil="Blank";}
	else if ($valnya=="mnuMasterItem") {$hasil="Master Item";}
	else if ($valnya=="mnuMasterMesin") {$hasil="Master Mesin";}
	else if ($valnya=="mnuMasterScrap") {$hasil="Master Scrap";}
	else if ($valnya=="mnuMasterWIP") {$hasil="Master WIP";}
	else if ($valnya=="mnuMasterSupplier") {$hasil="Master Supplier";}
	else if ($valnya=="mnuMStyle") {$hasil="Master Style";}
	else if ($valnya=="mnuBPB") {$hasil="Proses Penerimaan Bahan Baku";}
	else if ($valnya=="mnuBPBMesin") {$hasil="Proses Penerimaan Mesin";}
	else if ($valnya=="mnuBPBScrap") {$hasil="Proses Penerimaan Scrap";}
	else if ($valnya=="mnuBPBWIP") {$hasil="Proses Penerimaan WIP";}
	else if ($valnya=="mnuBPPB") {$hasil="Proses Pengeluaran Bahan Baku";}
	else if ($valnya=="mnuBPPBMesin") {$hasil="Proses Pengeluaran Mesin";}
	else if ($valnya=="mnuBPPBScrap") {$hasil="Proses Pengeluaran Scrap";}
	else if ($valnya=="mnuBPPBWIP") {$hasil="Proses Pengeluaran WIP";}
	else if ($valnya=="mnuBPBFG") {$hasil="Proses Penerimaan FG";}
	else if ($valnya=="mnuBPPBFG") {$hasil="Proses Pengeluaran FG";}
	else if ($valnya=="mnuRO") {$hasil="Proses Retur Keluar";}
	else if ($valnya=="mnuRI") {$hasil="Proses Retur Masuk";}
	else if ($valnya=="mnuWhsCode") {$hasil="mnuWhsCode";}
	else if ($valnya=="mnuPAdjust") {$hasil="Proses Adjustment";}
	else if ($valnya=="mnuPStockOpname") {$hasil="Proses Stock Opname";}
	else if ($valnya=="update_price_dok_pab") {$hasil="Update Price Dok Pabean";}
	else if ($valnya=="unlock_trans") {$hasil="unlock_trans";}
	else if ($valnya=="mnuPO") {$hasil="mnuPO";}
	else if ($valnya=="approve_po") {$hasil="approve_po";}
	else if ($valnya=="mnuMasterBenang") {$hasil="Master Benang";}
	else if ($valnya=="mnuMasterWarna") {$hasil="Master Warna";}
	else if ($valnya=="mnuBPPBReq") {$hasil="mnuBPPBReq";}
	else if ($valnya=="master_customer") {$hasil="Master Customer";}
	else if ($valnya=="import_tpb_in") {$hasil="import_tpb_in";}
	else if ($valnya=="import_tpb_out") {$hasil="import_tpb_out";}
	else if ($valnya=="in_rekap") {$hasil="in_rekap";}
	else if ($valnya=="out_rekap") {$hasil="out_rekap";}
	else if ($valnya=="list_detail") {$hasil="list_detail";}
	else if ($valnya=="calc_stock") {$hasil="calc_stock";}
	else if ($valnya=="rubah_type") {$hasil="rubah_type";}
	else if ($valnya=="rubah_seri") {$hasil="rubah_seri";}
	else if ($valnya=="bppb_prob") {$hasil="bppb_prob";}
	else if ($valnya=="BPPB_23") {$hasil="BPPB_23";}
	else if ($valnya=="change_pass") {$hasil="change_pass";}
	else if ($valnya=="mutasi_wip") {$hasil="mutasi_wip";}
	else if ($valnya=="input_stock_opname") {$hasil="input_stock_opname";}
	else if ($valnya=="update_tgl_trans") {$hasil="update_tgl_trans";}
	else if ($valnya=="master_whs") {$hasil="Master Warehouse";}
	else if ($valnya=="bppb_antar_whs") {$hasil="bppb_antar_whs";}
	else if ($valnya=="notif_expired") {$hasil="notif_expired";}
	else if ($valnya=="bpb_bom") {$hasil="bpb_bom";}
	else if ($valnya=="bppb_bom") {$hasil="bppb_bom";}
	else if ($valnya=="bom") {$hasil="bom";}
	else if ($valnya=="konfirmasi_sj") {$hasil="konfirmasi_sj";}
	else if ($valnya=="bppb_req") {$hasil="bppb_req";}
	else if ($valnya=="bpb_roll") {$hasil="bpb_roll";}
	else if ($valnya=="konv_unit") {$hasil="konv_unit";}
	else if ($valnya=="qc_pass") {$hasil="qc_pass";}
	else if ($valnya=="upload_po") {$hasil="upload_po";}
	else if ($valnya=="monitoring") {$hasil="Main Menu Monitoring";}
	else if ($valnya=="bpb_po") {$hasil="bpb_po";}
	else if ($valnya=="master_season") {$hasil="Master Season";}
	else if ($valnya=="bom_erp") {$hasil="bom_erp";}
	else if ($valnya=="costing") {$hasil="Main Menu Marketing";}
	else if ($valnya=="pay_terms") {$hasil="pay_terms";}
	else if ($valnya=="act_costing") {$hasil="Proses Costing";}
	else if ($valnya=="pre_cost") {$hasil="pre_cost";}
	else if ($valnya=="quote_inq") {$hasil="quote_inq";}
	else if ($valnya=="inventory") {$hasil="Main Menu Inventory";}
	else if ($valnya=="purchasing") {$hasil="Main Menu Purchasing";}
	else if ($valnya=="master") {$hasil="Main Menu Master Data";}
	else if ($valnya=="menu_master") {$hasil="Menu Master";}
	else if ($valnya=="menu_in") {$hasil="menu_in";}
	else if ($valnya=="menu_out") {$hasil="menu_out";}
	else if ($valnya=="production") {$hasil="Main Menu Production";}
	else if ($valnya=="generate_kode") {$hasil="generate_kode";}
	else if ($valnya=="closing") {$hasil="closing";}
	else if ($valnya=="update_dok_pab") {$hasil="update_dok_pab";}
	else if ($valnya=="hr") {$hasil="Main Menu HR";}
	else if ($valnya=="hr_masteremployee") {$hasil="Master Employee";}
	else if ($valnya=="hr_lokasi") {$hasil="hr_lokasi";}
	else if ($valnya=="hr_jabatan") {$hasil="hr_jabatan";}
	else if ($valnya=="hr_empfam") {$hasil="hr_empfam";}
	else if ($valnya=="hr_ptkp") {$hasil="hr_ptkp";}
	else if ($valnya=="hr_pro_abs") {$hasil="hr_pro_abs";}
	else if ($valnya=="hr_kode_absen") {$hasil="hr_kode_absen";}
	else if ($valnya=="hr_holiday") {$hasil="hr_holiday";}
	else if ($valnya=="hr_salary") {$hasil="hr_salary";}
	else if ($valnya=="hr_man_abs") {$hasil="hr_man_abs";}
	else if ($valnya=="hr_rekal") {$hasil="Rekalkulasi Gaji";}
	else if ($valnya=="link_to_bom") {$hasil="Link To BOM";}
	else if ($valnya=="m_bank") {$hasil="Master Bank";}
	else if ($valnya=="m_rate") {$hasil="Master Rate Currency";}
	else if ($valnya=="m_product") {$hasil="Master Product";}
	else if ($valnya=="m_shipmode") {$hasil="Master Ship Mode";}
	else if ($valnya=="m_complex") {$hasil="Master Complexity";}
	else if ($valnya=="sales_order") {$hasil="Sales Order";}
	else if ($valnya=="m_others") {$hasil="Master Others Cost";}
	else if ($valnya=="work_sheet") {$hasil="Work Sheet";}
	else if ($valnya=="bom_jo") {$hasil="bom_jo";}
	else if ($valnya=="purch_req") {$hasil="purch_req";}
	else if ($valnya=="purch_ord") {$hasil="purch_ord";}
	else if ($valnya=="hr_spl") {$hasil="hr_spl";}
	else if ($valnya=="approval") {$hasil="Main Menu Approval";}
	else
	{$hasil=$valnya;}
	return $hasil;
};

function cek_title($ket)
{	
	return $hasil;
};

function calc_stock($Mat_Type,$Id_Item)
{	If ($Mat_Type == "FG")
	{	$cbomat = $Mat_Type;
		
		$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno like 'FG%' and id_item='$Id_Item' group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		$tot_bpb_nett = round($rs['tot_bpb_nett'],2);
		$unit = $rs['unit'];

		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where bppbno like 'SJ-FG%' and id_item='$Id_Item' group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		$tot_bppb_nett = round($rs['tot_bppb_nett'],2);

		$stock = round(($tot_bpb - $tot_bppb),2);
		$stock_nett = round(($tot_bpb_nett - $tot_bppb_nett),2);
	}
	Else
	{	$cbomat = $Mat_Type;
		
		$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno not like 'FG%' and bpbno like '$cbomat%' and id_item='$Id_Item' 
			group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		$tot_bpb_nett = round($rs['tot_bpb_nett'],2);
		$unit = $rs['unit'];

		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where bppbno not like 'SJ-FG%' and bppbno like 'SJ-$cbomat%' and id_item='$Id_Item' 
			group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		$tot_bppb_nett = round($rs['tot_bppb_nett'],2);

		$stock = round(($tot_bpb - $tot_bppb),2);
		$stock_nett = round(($tot_bpb_nett - $tot_bppb_nett),2);
	}
	$cek = flookup("mattype","stock","mattype='$cbomat' and id_item='$Id_Item'");
	if ($cek=="") 
		{ mysql_query ("insert into stock (mattype,id_item,bpb,bppb,stock,unit,qty_karton,bpb_nett,bppb_nett,stock_nett) 
			values ('$cbomat','$Id_Item','$tot_bpb','$tot_bppb','$stock','$unit','0','$tot_bpb_nett','$tot_bppb_nett',
			'$stock_nett')"); 
		}
	else
		{ mysql_query ("update stock set bpb='$tot_bpb',bppb='$tot_bppb',stock='$stock',qty_karton='0',
		 	bpb_nett='$tot_bpb_nett',bppb_nett='$tot_bppb_nett',stock_nett='$stock_nett' where mattype='$cbomat' 
		 	and id_item='$Id_Item'"); 
		}
};

function calc_stock_new($Mat_Type,$Id_Item)
{	If ($Mat_Type == "FG")
	{	$cbomat = $Mat_Type;
		
		$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno like 'FG%' and id_item='$Id_Item' group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		$tot_bpb_nett = round($rs['tot_bpb_nett'],2);
		$unit = $rs['unit'];

		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where bppbno like 'SJ-FG%' and id_item='$Id_Item' group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		$tot_bppb_nett = round($rs['tot_bppb_nett'],2);

		$stock = round(($tot_bpb - $tot_bppb),2);
		$stock_nett = round(($tot_bpb_nett - $tot_bppb_nett),2);
	}
	Else
	{	$cbomat = $Mat_Type;
		
		$sql="select sum(qty) tot_bpb,sum(berat_bersih) tot_bpb_nett,unit from bpb where bpbno not like 'FG%' and bpbno like '$cbomat%' and id_item='$Id_Item' 
			group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bpb = round($rs['tot_bpb'],2);
		$tot_bpb_nett = round($rs['tot_bpb_nett'],2);
		$unit = $rs['unit'];

		$sql="select sum(qty) tot_bppb,sum(berat_bersih) tot_bppb_nett from bppb where bppbno not like 'SJ-FG%' and bppbno like 'SJ-$cbomat%' and id_item='$Id_Item' 
			group by id_item";
		$strsql = mysql_query($sql);
		if (!$strsql) { die($quenya. mysql_error()); }
		$rs = mysql_fetch_array($strsql);
		$tot_bppb = round($rs['tot_bppb'],2);
		$tot_bppb_nett = round($rs['tot_bppb_nett'],2);

		$stock = round(($tot_bpb - $tot_bppb),2);
		$stock_nett = round(($tot_bpb_nett - $tot_bppb_nett),2);
	}
	$cek = flookup("mattype","stock","mattype='$cbomat' and id_item='$Id_Item'");
	if ($cek=="") 
		{ mysql_query ("insert into stock (mattype,id_item,bpb,bppb,stock,unit,qty_karton,bpb_nett,bppb_nett,stock_nett) 
			values ('$cbomat','$Id_Item','$tot_bpb','$tot_bppb','$stock','$unit','0','$tot_bpb_nett','$tot_bppb_nett',
			'$stock_nett')"); 
		}
	else
		{ mysql_query ("update stock set bpb='$tot_bpb',bppb='$tot_bppb',stock='$stock',qty_karton='0',
		 	bpb_nett='$tot_bpb_nett',bppb_nett='$tot_bppb_nett',stock_nett='$stock_nett' where mattype='$cbomat' 
		 	and id_item='$Id_Item'"); 
		}
};

function calc_timecard($nik_nya,$tanggal_nya)
{
	$rs = mysql_fetch_array(mysql_query("select * from hr_timecard where empno='$nik_nya' and workdate='$tanggal_nya'"));
	$masuk = new DateTime($rs["TimeIn"]);
	$pulang = new DateTime($rs["TimeOut"]);
	
	$bts_masuk = new DateTime(flookup("jam_masuk","hr_parameter","jam_masuk<>''"));
	$bts_pulang1 = new DateTime(flookup("jam_pulang","hr_parameter","jam_masuk<>''"));
	$bts_pulang2 = new DateTime(flookup("jam_pulang_2","hr_parameter","jam_masuk<>''"));
	$bts_pulang3 = new DateTime(flookup("jam_pulang_3","hr_parameter","jam_masuk<>''"));
	$bts_pulang4 = new DateTime(flookup("jam_pulang_4","hr_parameter","jam_masuk<>''"));
	$bts_pulang5 = new DateTime(flookup("jam_pulang_5","hr_parameter","jam_masuk<>''"));
	
	//SESUAIKAN DENGAN PARAMETER UNTUK PERHITUNGAN JAM KERJA DAN OT 
	if ($masuk<$bts_masuk) {$par_masuk = $bts_masuk;} else {$par_masuk = $masuk;}
	if ($pulang>=$bts_pulang1 AND $pulang<$bts_pulang2) 
		{$par_pulang = $bts_pulang1;}
	else if ($pulang>=$bts_pulang2 AND $pulang<$bts_pulang3) 
		{$par_pulang = $bts_pulang2;}
	else if ($pulang>=$bts_pulang3 AND $pulang<$bts_pulang4) 
		{$par_pulang = $bts_pulang3;} 
	else if ($pulang>=$bts_pulang4 AND $pulang<$bts_pulang5) 
		{$par_pulang = $bts_pulang4;} 
	else if ($pulang>=$bts_pulang5) 
		{$par_pulang = $bts_pulang5;} 
	else 
		{$par_pulang = $pulang;}
					
	if ($par_pulang>=$bts_pulang1 AND $par_pulang<$bts_pulang2) {$break1 = flookup("istirahat","hr_parameter","jam_masuk<>''");} else {$break1=0;}
	if ($par_pulang>=$bts_pulang2 AND $par_pulang<$bts_pulang3) {$break2 = flookup("istirahat+istirahat_2","hr_parameter","jam_masuk<>''");} else {$break2=0;}
	if ($par_pulang>=$bts_pulang3 AND $par_pulang<$bts_pulang4) {$break3 = flookup("istirahat+istirahat_2+istirahat_3","hr_parameter","jam_masuk<>''");} else {$break3=0;}
	if ($par_pulang>=$bts_pulang4 AND $par_pulang<$bts_pulang5) {$break4 = flookup("istirahat+istirahat_2+istirahat_3+istirahat_4","hr_parameter","jam_masuk<>''");} else {$break4=0;}
	if ($par_pulang>=$bts_pulang5) {$break5 = flookup("istirahat+istirahat_2+istirahat_3+istirahat_4+istirahat_5","hr_parameter","jam_masuk<>''");} else {$break5=0;}
	
	$sel_jam = round($par_pulang->diff($par_masuk)->format('%h'),2); 
	$sel_menit = round($par_pulang->diff($par_masuk)->format('%i'),2); 
	$sel_menit = round(($sel_menit / 60),2);
	
	$harike = date("w",strtotime($tanggal_nya));
	if ($harike=6 AND $pulang<='12:30') {$break1 = 0; $break2 = 0; $break3 = 0; $break4 = 0; $break5 = 0;}
	if ($harike=6) 
		{$jam_kerja_normal = flookup("jam_kerja_normal_sabtu","hr_parameter","jam_masuk<>''");}
	else
		{$jam_kerja_normal = flookup("jam_kerja_normal","hr_parameter","jam_masuk<>''");}
	
	$jam_kerja = ($sel_jam + $sel_menit) - ($break1+$break2+$break3+$break4+$break5);
	if (round(round($jam_kerja,2)-round($jam_kerja),2)==0.01) {$jam_kerja=round($jam_kerja);}
	$overtime = 0;
	$payot = 0;
	if (flookup("holiday_date","hr_masterholiday","holiday_date='$tanggal_nya'")!="") {$hari_libur = "Y";} else {$hari_libur = "N";}
	if ($hari_libur == "N")
	{
		if($harike==0) {$hari_libur = "Y";} else {$hari_libur = "N";}
	}
	if ($hari_libur=="Y")
	{
		if ($jam_kerja>0)
		{
			$overtime = $jam_kerja;
			if ($overtime>=7) { $payot = 7 * 2;}
			if ($overtime>=8) { $payot = $payot + (3 * 1);}
			if ($overtime>=9) { $payot = $payot + (4 * ($overtime-8));}
		}
	}
	else
	{
		if ($jam_kerja>$jam_kerja_normal)
		{
			$overtime = $jam_kerja - $jam_kerja_normal;
			if ($overtime>=1) { $payot = 1.5; }
			if ($overtime>=2) { $payot = $payot + (2 * ($overtime-1)); }
		}
	}
	mysql_query("update hr_timecard set workhour='$jam_kerja',overtime='$overtime',payovertime='$payot' where empno='$nik_nya' and workdate='$tanggal_nya'");
};

function fn($angka,$decimal)
{	$format_angka = number_format($angka,$decimal);
	return $format_angka;
};

function unfn($angka)
{	$format_angka = str_replace(",","",$angka);
	return $format_angka;
};

function insert_temp_perdok($query,$usernya,$sesinya,$deldulu)
{	if ($deldulu=="Y") { mysql_query("delete from upload_standard where username='$usernya' and sesi='$sesinya'"); }
	$sqlnya = mysql_query($query);
	if (!$sqlnya) { die($query. mysql_error()); }
	while($data = mysql_fetch_array($sqlnya))
	{	$jenis_dok = $data['jenis_dokumen'];
		$no_dok = $data['bcno'];
		$tgl_dok = $data['bcdate'];
		$no_trans = $data['trans_no'];
		$tgl_trans = $data['trans_date'];
		$nm_sup = $data['supplier'];
		$kode_barang = $data['kode_brg']; #dapatkan id mahasiswa dari data array (row) 'id'
		$nm_barang = $data['itemdesc']; #dapatkan nama mahasiswa dari data array (row) 'nama'
		$satuan = $data['unit']; #dapatkan jurusan mahasiswa dari data array (row) 'jurusan' 
		$jml = $data['qty'];
		$curr = $data['curr'];
		$nilai = $data['nilai_barang'];
		
		mysql_query("insert into upload_standard 
			(JENIS_DOKUMEN,BCNO,BCDATE,SUPPLIER,KODE_BARANG,ITEMDESC,UNIT,QTY,CURR,PRICE,JENIS_BARANG,AREA_SUPPLIER,
			STATUS_KB_SUPPLIER,username,sesi,nm_file,berat_bersih,podate)
			values (trim('$jenis_dok'),trim('$no_dok'),'$tgl_dok',trim('$nm_sup'),trim('$kode_barang'),trim('$nm_barang'),
			trim('$satuan'),'$jml',trim('$curr'),'$nilai','$no_trans','','',
			trim('$usernya'),'$sesinya','',0,'$tgl_trans')");
	}
};

function insert_temp_perdok_new($query,$usernya,$sesinya,$deldulu)
{	if ($deldulu=="Y") { mysql_query("delete from upload_standard where username='$usernya' and sesi='$sesinya'"); }
	$sqlnya = mysql_query($query);
	if (!$sqlnya) { die($query. mysql_error()); }
	while($data = mysql_fetch_array($sqlnya))
	{	$jenis_dok = $data['jenis_dokumen'];
		$no_dok = $data['bcno'];
		$tgl_dok = $data['bcdate'];
		$no_trans = $data['trans_no'];
		$tgl_trans = $data['trans_date'];
		$nm_sup = $data['supplier'];
		$kode_barang = $data['kode_brg']; #dapatkan id mahasiswa dari data array (row) 'id'
		$nm_barang = $data['itemdesc']; #dapatkan nama mahasiswa dari data array (row) 'nama'
		$satuan = $data['unit']; #dapatkan jurusan mahasiswa dari data array (row) 'jurusan' 
		$jml = $data['qty'];
		$curr = $data['curr'];
		$nilai = $data['nilai_barang'];
		
		mysql_query("insert into upload_standard 
			(JENIS_DOKUMEN,BCNO,BCDATE,SUPPLIER,KODE_BARANG,ITEMDESC,UNIT,QTY,CURR,PRICE,JENIS_BARANG,AREA_SUPPLIER,
			STATUS_KB_SUPPLIER,username,sesi,nm_file,berat_bersih,podate)
			values (trim('$jenis_dok'),trim('$no_dok'),'$tgl_dok',trim('$nm_sup'),trim('$kode_barang'),trim('$nm_barang'),
			trim('$satuan'),'$jml',trim('$curr'),'$nilai','$no_trans','','',
			trim('$usernya'),'$sesinya','',0,'$tgl_trans')");
	}
};

function insert_temp_perdok_rekap($query,$usernya,$sesinya,$deldulu)
{	if ($deldulu=="Y") { mysql_query("delete from upload_standard where username='$usernya' and sesi='$sesinya'"); }
	$sqlnya = mysql_query($query);
	while($data = mysql_fetch_array($sqlnya))
	{	$jenis_dok = $data['jenis_dokumen'];
		$no_dok = $data['bcno'];
		$tgl_dok = $data['bcdate'];
		$no_trans = $data['trans_no'];
		$tgl_trans = $data['trans_date'];
		$nm_sup = $data['supplier'];
		$kode_barang = $data['kode_brg']; #dapatkan id mahasiswa dari data array (row) 'id'
		$nm_barang = $data['itemdesc']; #dapatkan nama mahasiswa dari data array (row) 'nama'
		$satuan = $data['unit']; #dapatkan jurusan mahasiswa dari data array (row) 'jurusan' 
		$jml = $data['qty'];
		$curr = $data['curr'];
		$nilai = $data['nilai_barang'];
		$berat_bersih = $data['berat_bersih'];
		$berat_kotor = $data['berat_kotor'];
		$nomor_aju = $data['nomor_aju'];
		$tujuan = $data['tujuan'];

		mysql_query("insert into upload_standard 
			(JENIS_DOKUMEN,BCNO,BCDATE,SUPPLIER,KODE_BARANG,ITEMDESC,UNIT,QTY,CURR,PRICE,JENIS_BARANG,AREA_SUPPLIER,
			STATUS_KB_SUPPLIER,username,sesi,nm_file,berat_bersih,podate,berat_kotor,nomor_aju,
			tujuan) 
			values (trim('$jenis_dok'),trim('$no_dok'),'$tgl_dok',trim('$nm_sup'),trim('$kode_barang'),trim('$nm_barang'),
			trim('$satuan'),'$jml',trim('$curr'),'$nilai','$no_trans','','',
			trim('$usernya'),'$sesinya','','$berat_bersih','$tgl_trans','$berat_kotor',
			'$nomor_aju','$tujuan')");
	}
};

function show_roll($from,$to,$bts,$ket)
{	if (substr($ket,0,8)=="CSBD_BOM")
	{	$x=$from;
		$arrket=explode("|",$ket);
	 	$id_contents=$arrket[2];
	 	$id_jo=$arrket[1];
		$rulebom=$arrket[3];
	 	$from2 = $from - 1;
	 	if ($rulebom=="All Color All Size")
		{	$sql="select 'All Color All Size' tampil 
				from jo_det a inner join so_det s 
				on a.id_so=s.id_so where s.cancel='N' and id_jo='$id_jo' 
				limit 1";
		}
		else if ($rulebom=="All Color Range Size")
		{	$sql="select concat('All Color | ',size) tampil 
				from jo_det a inner join so_det s 
				on a.id_so=s.id_so where s.cancel='N' and id_jo='$id_jo' 
				group by size order by s.id limit $from2,$to";
		}
		else if ($rulebom=="Per Color All Size")
		{	$sql="select concat(color,' | All Size') tampil 
				from jo_det a inner join so_det s 
				on a.id_so=s.id_so where s.cancel='N' and id_jo='$id_jo' group by color
				order by s.id limit $from2,$to";
		}
		else
		{	$sql="select concat(color,' | ',size) tampil 
				from jo_det a inner join so_det s 
				on a.id_so=s.id_so where s.cancel='N' and id_jo='$id_jo' group by color,size
				order by s.id limit $from2,$to";
		}
		$rs=mysql_query($sql);
		while($data = mysql_fetch_array($rs))
		{ if ($x<=$to and $x<=$bts) 
			{ $type="text"; 
				$stra=" readonly value='$data[tampil]' ";
				$stra2=" ";
				echo "
				<td>
					<input type ='$type' $stra name='no_roll[$x]' id='rollajax' class='form-control rollclass'>
				</td>";
					if (substr($ket,0,8)=="CSBD_BOM")
					{	
						if (substr($ket,-1)=="P")
						{	$sql = "SELECT a.id_item isi,
		          concat(a.itemdesc,' ',
		          a.color,' ',a.size,' ',a.add_info) tampil
		          FROM masteritem a inner join mastercf s on a.matclass=s.cfdesc
		          where a.mattype='C' and s.id='$id_contents'
		          ORDER BY a.id_item DESC";
		          #echo $sql;
		        }
		     		else
						{	$sql = "SELECT j.id isi,
		          concat(f.nama_width,' ',
		          g.nama_length,' ',h.nama_weight,' ',i.nama_color,' ',j.nama_desc,' ',j.add_info) tampil
		          FROM mastergroup a inner join mastersubgroup s on a.id=s.id_group
		          inner join mastertype2 d on s.id=d.id_sub_group
		          inner join mastercontents e on d.id=e.id_type
		          inner join masterwidth f on e.id=f.id_contents 
		          inner join masterlength g on f.id=g.id_width
		          inner join masterweight h on g.id=h.id_length
		          inner join mastercolor i on h.id=i.id_weight
		          inner join masterdesc j on i.id=j.id_color
		          where e.id='$id_contents'
		          ORDER BY j.id DESC";
		     		}
		     		#echo $sql;
		     		echo "<td><select class='form-control select2' $stra2  name ='jml_roll[$x]' id='jmlajax' class='jmlclass'>";
						IsiCombo($sql,'','Pilih Item');
						echo "</select>";
					}
				echo"
				</td>";
			}
			$x++;
		}
	}
	else
	{	if (substr($ket,0,1)=="S")
		{ $id_so_arr=explode(":",$ket);
			$id_so=$id_so_arr[1];
			$price_so=flookup("fob","so","id='$id_so'"); 
			$sql="select distinct size from so_det where cancel='N' and id_so='$id_so'";
			$query = mysql_query($sql);
      $listsize = ""; 
      while($data = mysql_fetch_array($query))
      {	if ($listsize=="") 
    		{ $listsize=$data['size']; }
  			else
  			{ $listsize=$listsize.":".$data['size']; }
  		}
		}
		else
		{	$listsize="";	}
		for ($x = $from; $x <= $to; $x++)
		{ if ($x<=$bts) 
			{ if ($listsize!="")
				{	$listsizearr=explode(":",$listsize);
					if (isset($listsizearr[$x-1]))
					{	$defsize=$listsizearr[$x-1]; }
					else
					{	$defsize=""; }
				}
				else
				{	$defsize=""; }
				$type="text"; 
				if ($ket=="R") 
				{$stra=" style='width: 70px; text-align: right;' readonly value='$x' ";
				 $stra2=" style='width: 70px;' ";
				 $stra3=" style='width: 120px;' ";
				} 
				else 
				{$stra=" style='width: 50px;' ";
				 $stra2=" style='width: 40px; text-align: right;' ";
				 $stra3=" style='width: 50px;' ";
				}
				echo "
				<td>
					<input type ='$type' $stra name='no_roll[$x]' id='rollajax' class='form-control rollclass' value='$defsize'>
				</td>"; 
					if ($ket=="D")
					{	echo "<select class='select2 item_detclass' style='width: 150px;height: 27px;'  
							name ='item_det[$x]' id='item_detajax'>";
						IsiCombo("select id_item isi,
							concat(goods_code,'|',itemdesc,'|',color,'|',size) tampil 
							from masteritem",'','Pilih Item');
						echo "</select>";
					}
					else
					{	echo "
						<td>
							<input type ='$type' $stra2 name ='lot[$x]' id='lotajax' class='form-control lotclass'>
						</td>
						<td>
							<input type ='$type' $stra2 name ='jml_roll[$x]' id='jmlajax' class='form-control jmlclass' onchange='calc_konv()'>
						</td>
						<td>
							<input type ='$type' $stra2 name ='jml_rollk[$x]' id='jmlkajax' class='form-control jmlkclass' readonly>
						</td>
						<td>
							<input type ='$type' $stra2 name ='jmlf_roll[$x]' id='jmlfajax' class='form-control jmlfclass' onchange='calc_konv()'>
						</td>
						<td>
							<input type ='$type' $stra3 name ='bar_rollk[$x]' id='barajax' class='form-control barclass'>
						</td>
						<td>
							<select class='form-control select2 rakclass' $stra3  name ='rak_rollk[$x]' 
								id='rakajax'>";
							$sql="select id isi,concat(kode_rak,' ',nama_rak) tampil from master_rak 
								order by kode_rak";
							IsiCombo($sql,'','Pilih Rak');
							echo "
							</select>
						</td>";
					}
					if (substr($ket,0,1)=="S")
					{	echo "<input type ='$type' $stra2 name ='addqty[$x]' id='addqtyajax' class='form-control addqtyclass'>";	
						echo " <input type ='$type' style='width: 70px;' name ='barcode[$x]' id='barajax' class='barclass'>";	
						echo " <input type ='$type' $stra2 name ='pxdet[$x]' id='pxdetajax' class='pxdetclass' value='$price_so'> ";
					}
				echo "
				</td>";
			}
		}
	}
};

function show_roll_size($from,$to,$bts,$ket)
{	if (substr($ket,0,1)=="S")
	{ $id_so_arr=explode(":",$ket);
		$id_so=$id_so_arr[1];
		$price_so=flookup("fob","so","id='$id_so'"); 
		$sql="select distinct size from so_det where cancel='N' and id_so='$id_so'";
		$query = mysql_query($sql);
    $listsize = ""; 
    while($data = mysql_fetch_array($query))
    {	if ($listsize=="") 
  		{ $listsize=$data['size']; }
			else
			{ $listsize=$listsize.":".$data['size']; }
		}
	}
	else
	{	$listsize="";	}
	for ($x = $from; $x <= $to; $x++)
	{ if ($x<=$bts) 
		{ if ($listsize!="")
			{	$listsizearr=explode(":",$listsize);
				if (isset($listsizearr[$x-1]))
				{	$defsize=$listsizearr[$x-1]; }
				else
				{	$defsize=""; }
			}
			else
			{	$defsize=""; }
			$type="text"; 
			$stra=" style='width: 50px;' ";
			$stra2=" style='width: 40px; text-align: right;' ";
			$stra3=" style='width: 50px;' ";
			echo "
			<td>
				<input type ='$type' $stra name='no_roll[$x]' id='rollajax' class='form-control rollclass' value='$defsize'>
			</td>
			<td>
				<input type ='$type' $stra2 name ='jml_roll[$x]' id='jmlajax' class='form-control jmlclass' onchange='calc_konv()'>
			</td>";
			if (substr($ket,0,1)=="S")
			{	echo "<td><input type ='$type' $stra2 name ='addqty[$x]' id='addqtyajax' class='form-control addqtyclass'></td>";	
				echo "<td><input type ='$type' style='width: 70px;' name ='barcode[$x]' id='barajax' class='form-control barclass'></td>";	
				echo "<td><input type ='$type' $stra2 name ='pxdet[$x]' id='pxdetajax' class='form-control pxdetclass' value='$price_so'></td>";
			}
		}
	}
};

function show_color($from,$to,$bts,$capt)
{	for ($x = $from; $x <= $to; $x++)
	{ if ($x<=$bts) 
		{ $type="text"; 
			echo "<td style='padding-bottom: 1em;'>
			<input type ='$type' style='width: 80px;'	 name ='jml_".$capt."[$x]' id='".$capt."ajax' class='".$capt."class'>
			</td>";
		}
	}
};

function xgen_kartu_stock($user,$sesi,$id_item,$cribpb,$cribppb)
{	$SQL = "delete from upload_tpb where username='$user' and sesi='$sesi'";
	insert_log($SQL,$user);
	if (substr($id_item,0,1)!="G")
	{	$gb = ""; }
	else
	{	$id_item = str_replace("G","",$id_item);
		$gb = "Y";	
	}
	
	if ($gb=="")
	{	
		$fld="trans_no,username,sesi,id_item,tanggal_aju,nomor_aju,supplier,
	  	jumlah_satuan,id_supplier,kode_barang,
	  	harga_penyerahan,stock,URAIAN_DOKUMEN";	
		$fld2="'Out','$user','$sesi',id_item,bppbdate,concat(bppbno,' ',ifnull(jo_no,'')),
	  	s.supplier,0,'',sent_to,0,qty,concat(jenis_dok,' (',bcno,')')";
	}
	else
	{	
		$fld="berat_kotor,trans_no,username,sesi,id_item,tanggal_aju,nomor_aju,supplier,
	  	jumlah_satuan,id_supplier,kode_barang,
	  	harga_penyerahan,stock,URAIAN_DOKUMEN,tanggal_daftar";	
		$fld2="round(qty*price,2),'Out','$user','$sesi',id_item,bcdate,bcno,
	  	s.supplier,0,'',sent_to,0,qty,jenis_dok,bppbdate";
	}
	$SQL = "insert into upload_tpb ($fld) select $fld2 from 
		bppb a left join mastersupplier s on a.id_supplier=s.id_supplier 
		left join jo on a.id_jo=jo.id 
	  where id_item='$id_item' and $cribppb order by bppbdate";
	insert_log($SQL,$user);

	if ($gb=="")
	{	
		$fld="trans_no,username,sesi,id_item,tanggal_aju,nomor_aju,supplier,jumlah_satuan,id_supplier
	  	,kode_barang,harga_penyerahan,stock,URAIAN_DOKUMEN,pono";
		$fld2="'In','$user','$sesi',id_item,bpbdate,concat(bpbno,' ',ifnull(jo_no,'')),s.supplier,0,s.supplier,
	  	'' kode_barang,qty,0,concat(jenis_dok,' (',bcno,')'),
	  	concat(pono,' / ',styleno)";
	}
	else
	{	
		$fld="berat_bersih,kode_satuan,trans_no,username,sesi,id_item,tanggal_aju,nomor_aju,supplier,jumlah_satuan,id_supplier
	  	,kode_barang,harga_penyerahan,stock,URAIAN_DOKUMEN,pono,tanggal_daftar";
		$fld2="round(qty*price,2),unit,'In','$user','$sesi',id_item,bcdate,bcno,s.supplier,0,s.supplier,
	  	'' kode_barang,qty,0,jenis_dok,
	  	concat(pono,' / ',styleno),bpbdate";
	}
	$SQL = "insert into upload_tpb ($fld) select $fld2 from 
		bpb a left join mastersupplier s on a.id_supplier=s.id_supplier 
		left join jo on a.id_jo=jo.id 
	  where id_item='$id_item' and $cribpb order by bpbdate";
	insert_log($SQL,$user);
	if ($gb=="Y")
	{	insert_log("SET @runtot:=0",$user);
		insert_log("SET @runtotamt:=0",$user);
    insert_log("SET @urut:=0",$user);
    $SQL = "insert into gb_posisiperdok 
    	(username,sesi,qtybtb,qtybkb,sisa,sisa_amt,urut,jdokmsk,bcno,bcdate,bpbdate,
    	kditem,itemdesc,unitbtb,amtbtb,jdokkel,bcno_kel,bcdate_kel,
    	bppbdate,amtbkb) 
    	SELECT '$user','$sesi',q1.masuk,q1.keluar,(@runtot := @runtot + (q1.masuk-q1.keluar)) AS rt,
    	(@runtotamt := @runtotamt + (q1.amt_btb-q1.amt_bkb)) AS rtant,
    	(@urut := @urut + 1) AS urut,
    	if(q1.jtrans='In',URAIAN_DOKUMEN,''),if(q1.jtrans='In',nomor_aju,''),
    	if(q1.jtrans='In',tanggal_aju,''),if(q1.jtrans='In',tanggal_daftar,''),kditem,
    	itemdesc,kode_satuan,q1.amt_btb,
    	if(q1.jtrans='Out',URAIAN_DOKUMEN,''),if(q1.jtrans='Out',nomor_aju,''),
    	if(q1.jtrans='Out',tanggal_aju,''),if(q1.jtrans='Out',tanggal_daftar,''),
    	q1.amt_bkb
      FROM
      ( select trans_no jtrans,URAIAN_DOKUMEN,NOMOR_AJU,TANGGAL_AJU,tanggal_daftar,
      	pono,SUPPLIER,'' blank2 ,round(HARGA_PENYERAHAN,2) masuk,round(STOCK,2) keluar,
      	s.goods_code kditem,s.itemdesc,a.kode_satuan,berat_bersih amt_btb,berat_kotor amt_bkb 
        from upload_tpb a inner join masteritem s on a.id_item=s.id_item 
        where a.username='$user' and sesi='$sesi' 
        order by TANGGAL_AJU asc, HARGA_PENYERAHAN desc
      ) AS q1 order by q1.TANGGAL_AJU asc, q1.masuk desc, q1.keluar desc";
  	insert_log($SQL,$user);
  }
};

function cek_minus_by_date($user,$sesi)
{	insert_log("SET @runtot:=0",$user);
  $SQL = "SELECT
    q1.URAIAN_DOKUMEN,q1.NOMOR_AJU,q1.TANGGAL_AJU,q1.pono,q1.SUPPLIER,q1.blank2, 
    q1.masuk,q1.keluar,
    (@runtot := @runtot + (q1.masuk-q1.keluar)) AS rt
    FROM
    ( select URAIAN_DOKUMEN,NOMOR_AJU,TANGGAL_AJU,
    	pono,SUPPLIER,'' blank2 ,round(HARGA_PENYERAHAN,2) masuk,round(STOCK,2) keluar 
      from upload_tpb a  
      where a.username='$user' and sesi='$sesi' 
      order by TANGGAL_AJU asc, HARGA_PENYERAHAN desc
    ) AS q1 order by q1.TANGGAL_AJU asc, q1.masuk desc, q1.keluar desc";
	$query = mysql_query($SQL);
	if (!$query) { die($SQL. mysql_error()); }
	while($data = mysql_fetch_array($query))
	{	if ($data['rt']<0)
		{	$cek=$data['rt'].":".fd_view($data['TANGGAL_AJU']);
			return $cek; 
			exit;
		}
	}
};

function get_nik($based,$thnmsk)
{	$thnmsk=substr($thnmsk,0,4);
	if ($based=="NAG") { $thnmsk="ALL"; }
	$hasil = flookup("nik","hr_nik","based='$based' and tahun='$thnmsk'") + 1;
	if ($hasil=='1')
		{ mysql_query ("insert into hr_nik (based,tahun,nik) values ('$based','$thnmsk','1')"); }
	else
		{ mysql_query ("update hr_nik set nik='$hasil' where based='$based' and tahun='$thnmsk'"); }
		
	$hasil = trim(substr($based,0,3).".".sprintf("%'.06d\n", $hasil));
	return $hasil;
};
?>
