<html>
<head>
    <title>Export Data Ke Excel </title>
</head>
<body>
    <style type="text/css">
    body{
        font-family: sans-serif;
    }
    table{
        margin: 20px auto;
        border-collapse: collapse;
    }
    table th,
    table td{
        border: 1px solid #3c3c3c;
        padding: 3px 8px;
 
    }
    a{
        background: blue;
        color: #fff;
        padding: 8px 10px;
        text-decoration: none;
        border-radius: 2px;
    }
    </style>
 
    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=format upload memorial journal.xls");
 ?> 
 
    <table style="width:100%;font-size:10px;" border="1" >
        <tr>
            <th style="text-align: center; vertical-align: middle;">No Journal</th>
             <th style="text-align: center; vertical-align: middle;">Tanggal Journal</th>
            <th style="text-align: center; vertical-align: middle;">Id Category</th>
            <th style="text-align: center; vertical-align: middle;">No Coa</th>
            <th style="text-align: center; vertical-align: middle;">No Cost</th>
            <th style="text-align: center; vertical-align: middle;">Center</th>
            <th style="text-align: center; vertical-align: middle;">No Reference</th>
            <th style="text-align: center; vertical-align: middle;">Tanggal Reference</th>                         
            <th style="text-align: center; vertical-align: middle;">No_ws</th>                                    
            <th style="text-align: center; vertical-align: middle;">Currency</th>
            <th style="text-align: center; vertical-align: middle;">Rate</th>                         
            <th style="text-align: center; vertical-align: middle;">Debit</th>                                    
            <th style="text-align: center; vertical-align: middle;">Credit</th>
            <th style="text-align: center; vertical-align: middle;">Debit IDR</th>                         
            <th style="text-align: center; vertical-align: middle;">Credit IDR</th>                                    
            <th style="text-align: center; vertical-align: middle;">Keterangan</th>
        </tr>
    </table>

</body>
</html>




