<?php
include '../../conn/conn.php';
ini_set('date.timezone', 'Asia/Jakarta');

$doc_number = $_POST['doc_number'];
$data = $_POST['data'];

// echo "< -- >";
// echo $no_kbon;


if(isset($data)){
	$query = "INSERT INTO supp_doc_temp (ref_doc,ket) 
VALUES 
	('$doc_number', '$data')";

$execute = mysqli_query($conn2,$query);
}

 $sql = mysqli_query($conn1,"select id,ref_doc,ket from supp_doc_temp");

$table = '<table id="mytdmodal" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;text-align:center;">
                    <thead>
                        <tr>                       
                            <th style="width:100px;">ID</th>
                            <th style="width:100px;">No Reff</th>
                            <th style="width:50px;">Ket</th>                                                         
                        </tr>
                    </thead>';

            $table .= '<tbody>';
			while ($row = mysqli_fetch_assoc($sql)) {
			
            $table .= '<tr>   
            <td style="" value="'.$row['id'].'">'.$row['id'].'</td>
                            <td style="" value="'.$row['ref_doc'].'">'.$row['ref_doc'].'</td>
                            <td style="" value="'.$row['ket'].'">'.$row['ket'].'</td>                    
                             
                       </tr>';
            $table .= '</tbody>';
        }
            $table .= '</table>';

echo $table;



if(!$execute){	
   die('Error: ' . mysqli_error());	
}else{
	
}

mysqli_close($conn2);
?>