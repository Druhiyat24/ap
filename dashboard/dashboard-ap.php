<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    .card-text {
        font-size: 14px;
        color: #2F4F4F;
    }
    .card {
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    background: linear-gradient(90deg, #ffffff, #e9e9e9);


}

.card-header {
    color: black;
    text-align: center;
    font-size: 15px;
    font-weight: bold;
    padding: 15px; /* Menambah jarak dalam header */
    white-space: nowrap; /* Agar teks tidak terpotong ke baris kedua */
    text-overflow: ellipsis; /* Jika teks sangat panjang, tampilkan "..." */
    overflow: hidden; /* Sembunyikan teks yang keluar dari batas */
    text-transform: uppercase;
}

.card-body {
    text-align: center;
    font-size: 14px;
    color: #2F4F4F;
}

.card-group .card {
    margin-bottom: 15px; /* Menambahkan margin bawah antara card */
}

.card-group {
    display: flex;
    justify-content: space-between; /* Agar kartu-kartu dalam grup terpisah rata */
    flex-wrap: wrap; /* Agar kartu-kartu berada di baris berikutnya pada ukuran layar kecil */
}

</style>

<div class="col p-4">
<!-- <div class="box " style="background-color: #F0F8FF;"> -->
    <div class="row">
    <div class="col-md-8">
        <div class="row">
        <div class="col-md-6">
            <div class="col p-2 ">
                <div class="card">

                        <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Net Purchase Year to Date</b></div>
                        <div class="card-body">
                        <?php
                        $sql_npch_ytd = mysqli_query($conn2,"select sum(total) total from (select sum(dpp) total from dsb_ap_purchase UNION select -sum(dpp) total from dsb_ap_retur) a");
                        $row = mysqli_fetch_array($sql_npch_ytd);
                        $total_npch_ytd = isset($row['total']) ? $row['total'] :0;

                        ?>
                            <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_npch_ytd,2); ?></b></p>
<!--                             <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
 -->                    </div>
                    </div>
                    <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Purchase Year to Date</b></div>
                            <div class="card-body">
                            <?php
                        $sql_pch_ytd = mysqli_query($conn2,"select sum(dpp) total from dsb_ap_purchase");
                        $row = mysqli_fetch_array($sql_pch_ytd);
                        $total_pch_ytd = isset($row['total']) ? $row['total'] :0;

                        ?>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_pch_ytd,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Purchase Return YTD</b></div>
                            <div class="card-body">
                            <?php
                        $sql_retur_ytd = mysqli_query($conn2,"select sum(dpp) total from dsb_ap_retur");
                        $row = mysqli_fetch_array($sql_retur_ytd);
                        $total_retur_ytd = isset($row['total']) ? $row['total'] :0;

                        ?>
                                    <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR (<?= number_format($total_retur_ytd,2); ?>)</b></p>
                            </div>
                    </div>
                </div>
                   
            </div>
        </div>
        <div class="col-md-6">
            <div class="col p-2">
                <div class="card">
                        <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Net Purchase Current Month</b></div>
                        <div class="card-body">
                            <?php
                        $sql_npch_cm = mysqli_query($conn2,"select sum(total) total from (select sum(dpp) total from dsb_ap_purchase where MONTH(tgl_bpb) = MONTH(CURRENT_DATE) UNION select -sum(dpp) total from dsb_ap_retur where MONTH(tgl_bpb) = MONTH(CURRENT_DATE)) a");
                        $row = mysqli_fetch_array($sql_npch_cm);
                        $total_npch_cm = isset($row['total']) ? $row['total'] :0;

                        ?>
                            <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_npch_cm,2); ?></b></p>
<!--                             <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
 -->                    </div>
                    </div>
                    <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Purchase Current Month</b></div>
                            <div class="card-body">
                                <?php
                        $sql_pch_cm = mysqli_query($conn2,"select sum(dpp) total from dsb_ap_purchase where MONTH(tgl_bpb) = MONTH(CURRENT_DATE) ");
                        $row = mysqli_fetch_array($sql_pch_cm);
                        $total_pch_cm = isset($row['total']) ? $row['total'] :0;

                        ?>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_pch_cm,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">Purchase Return Current Month</b></div>
                            <div class="card-body">
                                <?php
                        $sql_retur_cm = mysqli_query($conn2,"select sum(dpp) total from dsb_ap_retur where MONTH(tgl_bpb) = MONTH(CURRENT_DATE)");
                        $row = mysqli_fetch_array($sql_retur_cm);
                        $total_retur_cm = isset($row['total']) ? $row['total'] :0;

                        ?>
                                    <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR (<?= number_format($total_retur_cm,2); ?>)</b></p>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        </div>

        <div class="col p-2">
       <!--      <div class="card">
                <h3 class="card-title"><b><u>TOP 10 SUPPLIER BY AMOUNT (YTD)</u></b></h3>
                <div class="card-header bg-success border-dark text-white text-center"><b style="font-size: 15px;">TOP 10 SUPPLIER BY AMOUNT (YTD)</b></div>
                    <div class="card-body">
                        <div id="chart" style="height: 405px;"></div>
                    </div>
                </div> -->

                <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <b style="font-size:14px"><u>TOP 10 SUPPLIER BY AMOUNT (YTD)</u></b>
                                <!-- <select style="width:12rem" class="form-control" id="filter_dsb1" name="filter_dsb1" onchange="cari_ar_lokal_ekspor(this.value)">
                                        <option value="ALL">ALL</option>
                                        <option ></option>
                                </select> -->
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="position-relative mb-4">
                                <div id="chart_supptop10"></div>
                            </div>

                        </div>
                </div>
        </div>

    </div>
    <div class="col-md-4">
        <div class="col p-2">
            <div class="card">
                    <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select nama_supp,curr,sum(total_type_idr) total, sum(due_0) not_due, sum(produe_0) over_due from ((select  nama_supp,curr,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'kbn' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'lp' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr) a GROUP BY nama_supp,curr order by nama_supp asc)
                            UNION
                            (select  item_type2,curr,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a GROUP BY item_type2,curr order by item_type2 asc)) a");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $total = isset($row['total']) ? $row['total'] :0;
                        $not_due = isset($row['not_due']) ? $row['not_due'] :0;
                        $over_due = isset($row['over_due']) ? $row['over_due'] :0;

                    ?>
                        <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account payable Total Outstanding</b></div>
                        <div class="card-body">
                            <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total,2); ?></b></p>
<!--                             <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
 -->                    </div>
                    </div>
                    <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Not Due</b></div>
                            <div class="card-body">
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($not_due,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Over Due</b></div>
                            <div class="card-body">
                                    <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($over_due,2); ?></b></p>
                            </div>
                        </div>
                </div>
        </div>
            <div class="col p-2">
                <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Group</b></div>
                            <div class="card-body">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select nama_supp,curr,sum(total_type_idr) total, sum(due_0) not_due, sum(produe_0) over_due from ((select  nama_supp,curr,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'kbn' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'lp' id, nama_supp,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr) a GROUP BY nama_supp,curr order by nama_supp asc)) a");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $ap_group = isset($row['total']) ? $row['total'] :0;
                    ?>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($ap_group,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Non Group</b></div>
                            <div class="card-body">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select item_type2,curr,sum(total_type_idr) total, sum(due_0) not_due, sum(produe_0) over_due from ((select  item_type2,curr,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a GROUP BY item_type2,curr order by item_type2 asc)) a");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $ap_nongroup = isset($row['total']) ? $row['total'] :0;
                    ?>
                                    <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($ap_nongroup,2); ?></b></p>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col p-2">
                <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Machine</b></div>
                            <div class="card-body">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select  item_type2,curr,sum(total_type_idr) total, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a where item_type2 = 'MACHINE' GROUP BY item_type2 order by item_type2 asc");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $ap_machine = isset($row['total']) ? $row['total'] :0;
                    ?>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($ap_machine,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable Non Machine</b></div>
                            <div class="card-body">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select  item_type2,curr,sum(total_type_idr) total, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a where item_type2 = 'NON MACHINE' GROUP BY item_type2 order by item_type2 asc");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $ap_nonmachine = isset($row['total']) ? $row['total'] :0;
                    ?>
                                    <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format(($ap_nonmachine + $ap_group) ,2); ?></b></p>
                            </div>
                        </div>
                </div>
            </div>
            <div class="col p-2">
                <div class="card-group">
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable USD</b></div>
                            <div class="card-body">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select nama_supp,curr,sum(total_type) total_usd,sum(total_type_idr) total_idr, sum(due_0) not_due, sum(produe_0) over_due from (select * from ((select  nama_supp,curr,if(curr = 'USD',sum(total_type),0) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'kbn' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'lp' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr) a GROUP BY nama_supp,curr order by nama_supp asc)
                            UNION
                            (select  item_type2,curr,if(curr = 'USD',sum(total_type),0) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a GROUP BY item_type2,curr order by item_type2 asc)) a where curr = 'USD') a");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $total_usd = isset($row['total_usd']) ? $row['total_usd'] :0;
                        $total_eqvidr = isset($row['total_idr']) ? $row['total_idr'] :0;

                    ?>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>USD <?= number_format($total_usd,2); ?></b></p>
                                <p class="card-text" style="text-align: center;font-size: 14px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_eqvidr,2); ?></b></p>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header bg-info border-dark text-white text-center"><b style="font-size: 15px;">Account Payable IDR</b></div>
                            <div class="card-body align-middle">
                                <?php
                        $sql_ttl_ap = mysqli_query($conn2,"select nama_supp,curr,sum(total_type) total_usd,sum(total_type_idr) total_idr, sum(due_0) not_due, sum(produe_0) over_due from (select * from ((select  nama_supp,curr,if(curr = 'USD',sum(total_type),0) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'kbn' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr
                            UNION
                            select 'lp' id, nama_supp,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'GROUP' GROUP BY nama_supp,curr) a GROUP BY nama_supp,curr order by nama_supp asc)
                            UNION
                            (select  item_type2,curr,if(curr = 'USD',sum(total_type),0) total_type,sum(total_type_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from (select 'bpb' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_bpb where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'kbn' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_kbon where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr
                            UNION
                            select 'lp' id, item_type2,curr,sum(end_balance) total_type,sum(end_balance_idr) total_type_idr, sum(due_0) due_0, sum(produe_0) produe_0 from dsb_ap_lp where end_balance_idr != 0 and relasi = 'NON GROUP' GROUP BY item_type2,curr) a GROUP BY item_type2,curr order by item_type2 asc)) a where curr = 'IDR') a");
                        $row = mysqli_fetch_array($sql_ttl_ap);
                        $total_idr = isset($row['total_idr']) ? $row['total_idr'] :0;

                    ?>
                                    <p class="card-text" style="text-align: center;vertical-align: middle;font-size: 15px;color: #2F4F4F" onclick="showdata_slsytd()"><b>IDR <?= number_format($total_idr,2); ?></b></p>
                            </div>
                        </div>
                </div>
            </div>
    </div>
    <!-- <div class="col-md-12">
        <div class="col p-2">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <b style="font-size:14px"><u>TOP 10 SUPPLIER BY AMOUNT (YTD)</u></b>
                    </div>
                </div>
                <div class="card-body">
                    <div class="position-relative mb-4">
                        <div id="chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>
</div>
<!-- </div> -->



<script type="text/javascript">
    var options = {
        series: [{
            name: 'Value',
            data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(total) total from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase GROUP BY nama_supp 
                UNION 
                select nama_supp,-sum(dpp) total from dsb_ap_retur GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a");
                $row = mysqli_fetch_array($sql);
                $total = $row['total'];
                echo $total;
            ?>]
        }],
        chart: {
            height: 330,
            type: 'bar',
            toolbar: {
                show: false // Menyembunyikan toolbar default
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800, // Durasi animasi
                animateGradually: {
                    enabled: true,
                    delay: 150
                },
            },
        },
        plotOptions: {
            bar: {
                borderRadius: 5, // Membuat bar lebih bulat
                columnWidth: '70%',
                colors: {
                    ranges: [{
                        from: 0,
                        to: 100000,
                        color: '#00C0B0'  // Warna untuk bar rendah
                    }, {
                        from: 100000,
                        to: 500000,
                        color: '#00A1D1'  // Warna untuk bar sedang
                    }, {
                        from: 500000,
                        to: 1000000,
                        color: '#016FB9'  // Warna untuk bar tinggi
                    }]
                },
                dataLabels: {
                    position: 'top',
                    style: {
                        fontSize: '12px', // Menambahkan font lebih besar
                        fontWeight: 'bold',
                        colors: ['#ffffff'], // Memberikan warna putih pada data label
                    },
                },
                shadow: {
                    enabled: true,
                    blur: 5,
                    color: '#000',
                    opacity: 0.15
                }
            }
        },
         dataLabels: {
            enabled: true,
            formatter: function (val) {
                return "IDR " + val.toLocaleString('en-US');
            },
            offsetY: -20, // Menurunkan posisi data label sedikit
            style: {
                fontSize: '10px', // Mengubah ukuran font data label menjadi 10px
                colors: ["#304758"],
                fontFamily: 'Arial, sans-serif',
                fontWeight: 'bold',
            }
        },
        xaxis: {
            categories: [<?php 
                $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",nama_supp,"""")) nama_supp from (select nama_supp,round(sum(total),2) total from (select nama_supp,sum(dpp) total from dsb_ap_purchase GROUP BY nama_supp 
                UNION 
                select nama_supp,-sum(dpp) total from dsb_ap_retur GROUP BY nama_supp) a GROUP BY nama_supp order by total desc limit 10) a');
                $row = mysqli_fetch_array($sql);
                $nama_supp = $row['nama_supp'];
                echo $nama_supp;
            ?>],
            position: 'bottom',
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                fill: {
                    type: 'gradient',
                    gradient: {
                        colorFrom: '#D8E3F0',
                        colorTo: '#BED1E6',
                        stops: [0, 100],
                        opacityFrom: 0.4,
                        opacityTo: 0.5,
                    }
                }
            },
            tooltip: {
                enabled: true,
                style: {
                    fontSize: '10px',
                    fontWeight: 'bold'
                },
            },
            labels: {
                style: {
                    fontSize: '10px',
                    colors: ['#9e9e9e']
                },
            },
        },
        yaxis: {
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false,
            },
            labels: {
                show: false,
                formatter: function (val) {
                    return "IDR " + val.toLocaleString('en-US');
                }
            }
        },
        title: {
            text: '',
            floating: true,
            offsetY: 320,
            align: 'center',
            position: 'top',
            style: {
                color: '#444',
                fontSize: '18px',
                fontWeight: 'bold',
                fontFamily: 'Arial, sans-serif'
            }
        }
    };

    var chart = new ApexCharts(document.querySelector("#chart_supptop10"), options);
    chart.render();
</script>


<script type="text/javascript">
    var options = {
          series: [{
          name: 'NET PURCHASE',
          data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_purchase,0) - COALESCE(ttl_retur,0))/1000000,2)) net_purchase from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
(select sum(dpp)ttl_purchase,bln1 from (select dpp,MONTH(tgl_bpb) bln1 from dsb_ap_purchase ) a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
(select sum(dpp)ttl_retur,bln2 from (select dpp,MONTH(tgl_bpb) bln2 from dsb_ap_retur ) a GROUP BY bln2) c on c.bln2 = a.bulan");
            $row = mysqli_fetch_array($sql);
            $total = $row['net_purchase'];
            echo $total;
            
            ?>]
        }, {
          name: 'PAYMENT',
          data: [<?php 
                $sql = mysqli_query($conn2,"select GROUP_CONCAT(round((COALESCE(ttl_bpb,0) + COALESCE(ttl_kbon,0) + COALESCE(ttl_lp,0))/1000000,2)) payment from (select bulan,bulan_text,nama_bulan,nama_bulan_singkat,tahun from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a LEFT JOIN
(select bln1,sum(end_balance_idr) ttl_bpb from (select MONTH(tgl_bpb) bln1,end_balance_idr from dsb_ap_bpb where end_balance_idr != 0 and tgl_bpb >= '2024-01-01') a GROUP BY bln1) b on b.bln1 = a.bulan LEFT JOIN
(select bln2,sum(end_balance_idr) ttl_kbon from (select MONTH(tgl_kbon) bln2,end_balance_idr from dsb_ap_kbon where end_balance_idr != 0 and tgl_kbon >= '2024-01-01') a GROUP BY bln2) c on c.bln2 = a.bulan LEFT JOIN
(select bln3,sum(end_balance_idr) ttl_lp from (select MONTH(tgl_payment) bln3,end_balance_idr from dsb_ap_lp where end_balance_idr != 0 and tgl_payment >= '2024-01-01') a GROUP BY bln3) d on d.bln3 = a.bulan");
            $row = mysqli_fetch_array($sql);
            $total = $row['payment'];
            echo $total;
            
            ?>]
        }],
          chart: {
          type: 'bar',
          height: 350
        },
        plotOptions: {
          bar: {
            horizontal: false,
            columnWidth: '55%',
            endingShape: 'rounded'
          },
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          show: true,
          width: 2,
          colors: ['transparent']
        },
        xaxis: {
          categories: [<?php 
                $sql = mysqli_query($conn2,'select GROUP_CONCAT(concat("""",label,"""")) label from (select concat(nama_bulan_singkat," ",tahun) label from dim_date where tahun = YEAR(CURRENT_DATE) GROUP BY bulan ORDER BY bulan_text) a');
            $row = mysqli_fetch_array($sql);
            $label = $row['label'];
            echo $label;
            
            ?>],
        },
        yaxis: {
          title: {
            text: 'MIO'
          }
        },
        fill: {
          opacity: 1
        },
        tooltip: {
          y: {
            formatter: function (val) {
              return "IDR " + val + " MIO"
            }
          }
        }
        };

        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
</script>