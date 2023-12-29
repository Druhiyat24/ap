<?php
include '../../conn/conn.php';

$requestData= $_REQUEST;
$columns = array( 
	0 =>'no_kbon', 
	1 => 'tgl_kbon',
	2=> 'nama_supp'
);
//----------------------------------------------------------------------------------
//join 2 tabel dan bisa lebih, tergantung kebutuhan
$sql = " select no_kbon, tgl_kbon, nama_supp, SUM(subtotal) as sub, SUM(tax) as tax, SUM(total) as total, curr, create_user, status, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code";
$sql.= " FROM kontrabon";
$sql.= " group by no_kbon";
$query=mysqli_query($conn2,$sql);
$totalData = mysqli_num_rows($query);
$totalFiltered = $totalData;
 
//----------------------------------------------------------------------------------
$sql = " select no_kbon, tgl_kbon, nama_supp, SUM(subtotal) as sub, SUM(tax) as tax, SUM(total) as total, curr, create_user, status, tgl_tempo, no_faktur, supp_inv, tgl_inv, pph_code, SUM(pph_value) as pph_value, pph_code";
$sql.= " FROM kontrabon";
$sql.= " WHERE 1=1";
if( !empty($requestData['search']['value']) ) {
	//----------------------------------------------------------------------------------
	$sql.=" AND ( no_kbon LIKE '%".$requestData['search']['value']."' ";    
	$sql.=" OR nama_supp LIKE '".$requestData['search']['value']."%' )";
}
$sql.= " group by no_kbon";
//----------------------------------------------------------------------------------
$query=mysqli_query($conn2,$sql);	
$totalFiltered = mysqli_num_rows($query);
$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]." ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']." ";
$query=mysqli_query($conn2,$sql);
//----------------------------------------------------------------------------------
$data = array();
while( $row=mysqli_fetch_array($query) ) {
	$nestedData=array(); 
	$nestedData[] = $row["no_kbon"];
	$nestedData[] = $row["tgl_kbon"];
	$nestedData[] = $row["nama_supp"];
	$nestedData[] = number_format($row["sub"],2);
	$nestedData[] = number_format($row["tax"],2);
	$nestedData[] = number_format($row["pph_value"],2);
	$nestedData[] = number_format($row["total"],2);
	$nestedData[] = $row["curr"];
	$nestedData[] = $row["create_user"];
	$nestedData[] = $row["status"];
	$data[] = $nestedData;
}
//----------------------------------------------------------------------------------
$json_data = array(
			"draw"            => intval( $requestData['draw'] ),  
			"recordsTotal"    => intval( $totalData ), 
			"recordsFiltered" => intval( $totalFiltered ), 
			"data"            => $data );
//----------------------------------------------------------------------------------
echo json_encode($json_data);
?>