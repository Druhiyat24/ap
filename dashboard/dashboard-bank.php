<style>
    #chartdiv {
      width: 100%;
      height: 300px;
  }
  #chartdiv2 {
      width: 100%;
      height: 300px;
  }
  #chartdiv3 {
      width: 100%;
      height: 300px;
  }
  #chartdiv4 {
      width: 100%;
      height: 300px;
  }
  #chartdiv5 {
      width: 100%;
      height: 300px;
  }
  #chartdiv6 {
      width: 100%;
      height: 300px;
  }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="row p-3">
            <div class="col-md-4">
                <div class="card border-dark mb-2 mt-2" >
                    <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;"><button type="button" class="close text-white" onclick="openmodalcoh()"><span class="fa fa-bars"></span></button><b style="font-size: 0.9rem;">CASH ON HAND</b></div>
                    <div class="card-body text-secondary">
                        <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                            <?php
                            $bulan = date("M"); 
                            $tahun = date("Y"); 
                            $sql_coh = mysqli_query($conn2,"select no_coa,nama_coa,round(sum(saldo_$bulan),0) total from b_trial_balance_$tahun where no_coa IN ('1.01.01','1.01.02','1.01.03')");
                            $row_coh = mysqli_fetch_array($sql_coh);
                            $total_coh = isset($row_coh['total']) ? $row_coh['total'] :0;

                            ?>
                            IDR <?= number_format($total_coh,0); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-dark mb-2 mt-2" >
                        <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;" ><button type="button" class="close text-white" onclick="openmodalcib()"><span class="fa fa-bars"></span></button><b style="font-size: 0.9rem;">CASH IN BANK</b></div>
                        <div class="card-body text-secondary">
                            <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                            $bulan = date("M"); 
                            $tahun = date("Y");
                            $sql_cib = mysqli_query($conn2,"select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,if(saldo_$bulan > 0,saldo_$bulan,0) total from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02','1.10.11','1.10.21','1.10.31','1.10.81','1.10.82','1.10.83','1.10.84')) a");
                            $row_cib = mysqli_fetch_array($sql_cib);
                            $total_cib = isset($row_cib['total']) ? $row_cib['total'] :0;

                            ?>
                            IDR <?= number_format($total_cib,0); ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-dark mb-2 mt-2" >
                        <div class="card-header bg-gradient border-dark text-white" style="background-color:#006400;"><button type="button" class="close text-white" onclick="openmodaltc()"><span class="fa fa-bars"></span></button><b style="font-size: 0.9rem;">CASH & BANK TOTAL</b></div>
                        <div class="card-body text-secondary">
                            <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                                <?php
                                $total_cb = $total_coh + $total_cib 
                                ?>
                                IDR <?= number_format($total_cb,0); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-dark mb-2 mt-2" >
                            <div class="card-header bg-info bg-gradient bg-gradient border-dark text-white"><b style="font-size: 0.9rem;">BANK LOAN IDR</b></div>
                            <div class="card-body text-secondary">
                                <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                                $bulan = date("M"); 
                                $tahun = date("Y");
                                // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_$tahun where no_coa IN ('2.20.01')
                                //     UNION
                                //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_$tahun where no_coa IN ('1.10.01')) a");
                                // $row_bli = mysqli_fetch_array($sql_bli);
                                // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

                                //nilai new 
                                $sqlswl3_ = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-997-1979'");
                                $rowswl3_ = mysqli_fetch_array($sqlswl3_);
                                $swl3_ = isset($rowswl3_['amount']) ? $rowswl3_['amount'] : 0;

                                $sqlsaldo_ = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-997-1979'");
                                $rowsaldo_ = mysqli_fetch_array($sqlsaldo_);
                                $salawal_ = isset($rowsaldo_['amount']) ? $rowsaldo_['amount'] : 0;

                                $sqlswl4_ = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                    FROM
                                    (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date < CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                    (SELECT @runtot:= $salawal_ ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
                                $rowswl4_ = mysqli_fetch_array($sqlswl4_);
                                $saldoswal2_ = isset($rowswl4_['saldoawal']) ? $rowswl4_['saldoawal'] : 0;

                                $sql6_ = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                    FROM
                                    (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-997-1979' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                    (SELECT @runtot:= $saldoswal2_,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
                                $rows6_ = mysqli_fetch_array($sql6_);
                                $total_bli = isset($rows6_['saldo_akhir']) ? $rows6_['saldo_akhir'] : $saldoswal2_;

                                ?>
                                IDR <?= number_format(abs($total_bli),0); ?></p>
                            </div>
                        </div>
                        <div class="card border-dark mb-2" >
                            <div class="card-header border-dark"><b style="font-size: 1rem;">
                                <?php
                                $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
                                $row1 = mysqli_fetch_array($sql1);
                                $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

                                ?>
                                LIMIT : IDR <?= number_format($limit_idr,0); ?></b></div>
                                <div class="card-body text-secondary">
                                    <div id="chartdiv"></div>
                                </div>
                            </div>
                            <div class="card border-dark mb-2" >
                                <div class="card-header border-dark"><button type="button" class="close" onclick="openmodalloanidr()"><span class="fa fa-bars"></span></button><b style="font-size: 1rem;">LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b></div>
                                <div class="card-body text-secondary">
                                    <div id="chartdiv2"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card border-dark mb-2 mt-2" >
                                <div class="card-header bg-info bg-gradient bg-gradient border-dark text-white"><b style="font-size: 1.1rem;">BANK LOAN USD</b></div>
                                <div class="card-body text-secondary">
                                    <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F"><?php
                                    $bulan = date("M"); 
                                    $tahun = date("Y");
                                    $sql_blu = mysqli_query($conn2,"select total,(total / rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_$tahun where no_coa IN ('2.20.02')
                                        UNION
                                        select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_$tahun where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
                                    $row_blu = mysqli_fetch_array($sql_blu);
                                    $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
                                    $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

                                    //nilai new 
                                    $sqlswl3 = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-998-1982'");
                                    $rowswl3 = mysqli_fetch_array($sqlswl3);
                                    $swl3 = isset($rowswl3['amount']) ? $rowswl3['amount'] : 0;

                                    $sqlsaldo = mysqli_query($conn1,"select amount from b_saldoawal_bank where account = '008-998-1982'");
                                    $rowsaldo = mysqli_fetch_array($sqlsaldo);
                                    $salawal = isset($rowsaldo['amount']) ? $rowsaldo['amount'] : 0;

                                    $sqlswl4 = mysqli_query($conn1,"select nomor,saldo_akhir saldoawal from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                        FROM
                                        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date < CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                        (SELECT @runtot:= $salawal ,@runnum:= 0) runtot) a ORDER BY a.nomor desc limit 1");
                                    $rowswl4 = mysqli_fetch_array($sqlswl4);
                                    $saldoswal2 = isset($rowswl4['saldoawal']) ? $rowswl4['saldoawal'] : 0;

                                    $sql6 = mysqli_query($conn1, "select nomor,date,saldo_akhir from (SELECT (@runnum :=@runnum + 1) AS nomor,q1.date,q1.doc_num,q1.curr,q1.deskripsi,q1.credit,q1.debit, (@runtot :=@runtot + q1.debit - q1.credit) AS saldo_akhir
                                        FROM
                                        (select transaksi_date as date, no_doc as doc_num,deskripsi,debit,credit,curr from b_reportbank where akun = '008-998-1982' and transaksi_date between CURRENT_DATE() and CURRENT_DATE() and status != 'Cancel') AS q1 JOIN
                                        (SELECT @runtot:= $saldoswal2,@runnum:=0) runtot) a ORDER BY a.nomor desc limit 1");
                                    $rows6 = mysqli_fetch_array($sql6);
                                    $saldoakhir = isset($rows6['saldo_akhir']) ? $rows6['saldo_akhir'] : $saldoswal2;
                                    $dateakhir = isset($rows6['date']) ? $rows6['date'] : null;

                                    $sqlrates3 = mysqli_query($conn1,"select id,rate FROM masterrate where v_codecurr = 'PAJAK' and tanggal = '$dateakhir'");
                                    $rowrates3 = mysqli_fetch_array($sqlrates3);
                                    $maxidrate3 = isset($rowrates3['id']) ? $rowrates3['id'] : null;

                                    if ($maxidrate3 != null) {
                                        $rates3 = $rowrates3['rate'];
                                    }else{
                                        $sqlxss3 = mysqli_query($conn1,"select max(id) as id FROM masterrate where v_codecurr = 'PAJAK'");
                                        $rowxss3 = mysqli_fetch_array($sqlxss3);
                                        $maxidss3 = isset($rowxss3['id']) ? $rowxss3['id'] : null;

                                        $sqlyss3 = mysqli_query($conn1,"select ROUND(rate,2) as rate , tanggal  FROM masterrate where id = '$maxidss3' and v_codecurr = 'PAJAK'");
                                        $rowyss3 = mysqli_fetch_array($sqlyss3);
                                        $rates3 = isset($rowyss3['rate']) ? $rowyss3['rate'] : 1;
                                    }

                                    ?>
                                    USD <?= number_format(abs($saldoakhir),2); ?> ( IDR <?= number_format(abs($saldoakhir * $rates3),0); ?> )</p>
                                </div>
                            </div>
                            <div class="card border-dark mb-2" >
                                <div class="card-header border-dark"><b style="font-size: 1rem;">
                                    <?php
                                    $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
                                    $row1 = mysqli_fetch_array($sql1);
                                    $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
                                    $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

                                    ?>
                                    LIMIT : USD <?= number_format($fac_limit,0); ?> ( IDR <?= number_format($limit_convert,0); ?> )</b></div>
                                    <div class="card-body text-secondary">
                                        <div id="chartdiv3"></div>
                                    </div>
                                </div>
                                <div class="card border-dark mb-2" >
                                    <div class="card-header border-dark"><button type="button" class="close" onclick="openmodalloanusd()"><span class="fa fa-bars"></span></button><b style="font-size: 1rem;">LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b></div>
                                    <div class="card-body text-secondary">
                                        <div id="chartdiv4"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card border-dark mb-2 mt-2" >
                                    <div class="card-header bg-info bg-gradient border-dark text-white"><b style="font-size: 1.1rem;">BANK LOAN TOTAL</b></div>
                                    <div class="card-body text-secondary">
                                        <p class="card-text" style="text-align: center;font-size: 1.4rem;color: #2F4F4F">
                                            <?php
                                            $total_bl = abs($total_bli) + abs($saldoakhir * $rates3);
                                            ?>
                                            IDR <?= number_format($total_bl,0); ?></p>
                                        </div>
                                    </div>
                                    <div class="card border-dark mb-2" >
                                        <div class="card-header border-dark"><b style="font-size: 1rem;">
                                            <?php
                                            $total_limit = $limit_idr + $limit_convert;
                                            ?>
                                            LIMIT : IDR <?= number_format($total_limit,0); ?></b></div>
                                            <div class="card-body text-secondary">
                                                <div id="chartdiv5"></div>
                                            </div>
                                        </div>
                                        <div class="card border-dark mb-2" >
                                            <div class="card-header border-dark"><button type="button" class="close" onclick="openmodalloantotal()"><span class="fa fa-bars"></span></button><b style="font-size: 1rem;">LAST 3 MONTH LOAN BALANCE (IN IDR MIO)</b></div>
                                            <div class="card-body text-secondary">
                                                <div id="chartdiv6"></div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modalloanidr" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header bg-secondary bg-gradient text-white">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                    <h4 class="modal-title" id="text-tittle">IDR Loan Balance (Month over Month)</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="p-2">
                                        <div id="chartloanidr"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modalloanusd" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header bg-secondary bg-gradient text-white">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                                <h4 class="modal-title" id="text-tittle">USD Loan Balance (Month over Month)</h4>
                            </div>
                            <div class="modal-body">
                                <div class="p-2">
                                    <div id="chartloanusd"></div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal fade" id="modalloantotal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                          <div class="modal-header bg-secondary bg-gradient text-white">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                            <h4 class="modal-title" id="text-tittle">Total Loan Balance (Month over Month)</h4>
                        </div>
                        <div class="modal-body">
                            <div class="p-2">
                                <div id="chartloantotal"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="modal fade" id="modalcoh" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header bg-success bg-gradient text-white">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                        <h4 class="modal-title" id="text-tittle">Cash on Hand (Month over Month) in Million IDR</h4>
                    </div>
                    <div class="modal-body">
                        <div class="p-2">
                            <div id="chartcoh"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="modal fade" id="modalcib" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                  <div class="modal-header bg-success bg-gradient text-white">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                    <h4 class="modal-title" id="text-tittle">Cash in Banks (Month over Month) in Million IDR</h4>
                </div>
                <div class="modal-body">
                    <div class="p-2">
                        <div id="chartcib"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="modaltc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header bg-success bg-gradient text-white">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
                <h4 class="modal-title" id="text-tittle">Total Cash (Month over Month) in Million IDR</h4>
            </div>
            <div class="modal-body">
                <div class="p-2">
                    <div id="charttc"></div>
                </div>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="modaldetcoh" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            <h4 class="modal-title" id="jdl_coh"></h4>
        </div>
        <div class="modal-body">
            <div class="p-0">
                <div id="detail_coh"></div>
            </div>
        </div>

    </div>
</div>
</div>

<div class="modal fade" id="modaldetcib" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="width:380px;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            <h4 class="modal-title" id="jdl_cib"></h4>
        </div>
        <div class="modal-body">
            <div class="p-0">
                <div id="detail_cib"></div>
            </div>
        </div>

    </div>
</div>
</div>

<div class="modal fade" id="modaldettc" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="width:380px;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="fa fa-times"></span></button>
            <h4 class="modal-title" id="jdl_tc"></h4>
        </div>
        <div class="modal-body">
            <div class="p-0">
                <div id="detail_tc"></div>
            </div>
        </div>

    </div>
</div>
</div>

<script type="text/javascript">
    function openmodalloanidr(){
        $("#modalloanidr").modal("show");
    }

    function openmodalloanusd(){
        $("#modalloanusd").modal("show");
    }

    function openmodalloantotal(){
        $("#modalloantotal").modal("show");
    }

    function openmodalcoh(){
        $("#modalcoh").modal("show");
    }

    function openmodalcib(){
        $("#modalcib").modal("show");
    }

    function openmodaltc(){
        $("#modaltc").modal("show");
    }
</script>

<script>
    var options = {
      series: [{
          name: 'Total Cash',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");
              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select no_coa,nama_coa,round(sum(saldo_jan /1000000),2) saldo_jan,round(sum(saldo_feb /1000000),2) saldo_feb,round(sum(saldo_mar /1000000),2) saldo_mar,round(sum(saldo_apr /1000000),2) saldo_apr,round(sum(saldo_may /1000000),2) saldo_may,round(sum(saldo_jun /1000000),2) saldo_jun,round(sum(saldo_jul /1000000),2) saldo_jul,round(sum(saldo_aug /1000000),2) saldo_aug,round(sum(saldo_sep /1000000),2) saldo_sep,round(sum(saldo_oct /1000000),2) saldo_oct,round(sum(saldo_nov /1000000),2) saldo_nov,round(sum(saldo_dec /1000000),2) saldo_dec from (select no_coa,nama_coa,if(saldo_jan > 0,saldo_jan,0) saldo_jan,if(saldo_feb > 0,saldo_feb,0) saldo_feb,if(saldo_mar > 0,saldo_mar,0) saldo_mar,if(saldo_apr > 0,saldo_apr,0) saldo_apr,if(saldo_may > 0,saldo_may,0) saldo_may,if(saldo_jun > 0,saldo_jun,0) saldo_jun,if(saldo_jul > 0,saldo_jul,0) saldo_jul,if(saldo_aug > 0,saldo_aug,0) saldo_aug,if(saldo_sep > 0,saldo_sep,0) saldo_sep,if(saldo_oct > 0,saldo_oct,0) saldo_oct,if(saldo_nov > 0,saldo_nov,0) saldo_nov,if(saldo_dec > 0,saldo_dec,0) saldo_dec from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02','1.10.11','1.10.21','1.10.31','1.10.81','1.10.82','1.10.83','1.10.84')
                UNION 
                select no_coa,nama_coa,saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('1.01.01','1.01.02','1.01.03')) a)a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_and_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title);
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                     console.log(xhr);
                 }
             });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#charttc"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash in Banks',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");
              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select no_coa,nama_coa,round(sum(saldo_jan /1000000),2) saldo_jan,round(sum(saldo_feb /1000000),2) saldo_feb,round(sum(saldo_mar /1000000),2) saldo_mar,round(sum(saldo_apr /1000000),2) saldo_apr,round(sum(saldo_may /1000000),2) saldo_may,round(sum(saldo_jun /1000000),2) saldo_jun,round(sum(saldo_jul /1000000),2) saldo_jul,round(sum(saldo_aug /1000000),2) saldo_aug,round(sum(saldo_sep /1000000),2) saldo_sep,round(sum(saldo_oct /1000000),2) saldo_oct,round(sum(saldo_nov /1000000),2) saldo_nov,round(sum(saldo_dec /1000000),2) saldo_dec from (select no_coa,nama_coa,if(saldo_jan > 0,saldo_jan,0) saldo_jan,if(saldo_feb > 0,saldo_feb,0) saldo_feb,if(saldo_mar > 0,saldo_mar,0) saldo_mar,if(saldo_apr > 0,saldo_apr,0) saldo_apr,if(saldo_may > 0,saldo_may,0) saldo_may,if(saldo_jun > 0,saldo_jun,0) saldo_jun,if(saldo_jul > 0,saldo_jul,0) saldo_jul,if(saldo_aug > 0,saldo_aug,0) saldo_aug,if(saldo_sep > 0,saldo_sep,0) saldo_sep,if(saldo_oct > 0,saldo_oct,0) saldo_oct,if(saldo_nov > 0,saldo_nov,0) saldo_nov,if(saldo_dec > 0,saldo_dec,0) saldo_dec from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02','1.10.11','1.10.21','1.10.31','1.10.81','1.10.82','1.10.83','1.10.84')) a)a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_in_bank.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_cib').html(data);
                        $('#jdl_cib').html(title);
                        $('#modaldetcib').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                     console.log(xhr);
                 }
             });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcib"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Cash On Hand',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select no_coa,nama_coa,round(sum(saldo_jan /1000000),2) saldo_jan,round(sum(saldo_feb /1000000),2) saldo_feb,round(sum(saldo_mar /1000000),2) saldo_mar,round(sum(saldo_apr /1000000),2) saldo_apr,round(sum(saldo_may /1000000),2) saldo_may,round(sum(saldo_jun /1000000),2) saldo_jun,round(sum(saldo_jul /1000000),2) saldo_jul,round(sum(saldo_aug /1000000),2) saldo_aug,round(sum(saldo_sep /1000000),2) saldo_sep,round(sum(saldo_oct /1000000),2) saldo_oct,round(sum(saldo_nov /1000000),2) saldo_nov,round(sum(saldo_dec /1000000),2) saldo_dec from b_trial_balance_$tahun where no_coa IN ('1.01.01','1.01.02','1.01.03')) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          events: {
            click: function(event, chartContext, config, val) {
              // The last parameter config contains additional information like `seriesIndex` and `dataPointIndex` for cartesian charts
              // alert(config.dataPointIndex);
              if (config.dataPointIndex >= 0) {
                  if (config.dataPointIndex == 0) {
                    var filter = 'saldo_jan';
                    var title = 'January';
                }else if(config.dataPointIndex == 1) {
                    var filter = 'saldo_feb';
                    var title = 'February';
                }else if(config.dataPointIndex == 2) {
                    var filter = 'saldo_mar';
                    var title = 'March';
                }else if(config.dataPointIndex == 3) {
                    var filter = 'saldo_apr';
                    var title = 'April';
                }else if(config.dataPointIndex == 4) {
                    var filter = 'saldo_may';
                    var title = 'May';
                }else if(config.dataPointIndex == 5) {
                    var filter = 'saldo_jun';
                    var title = 'June';
                }else if(config.dataPointIndex == 6) {
                    var filter = 'saldo_jul';
                    var title = 'July';
                }else if(config.dataPointIndex == 7) {
                    var filter = 'saldo_aug';
                    var title = 'August';
                }else if(config.dataPointIndex == 8) {
                    var filter = 'saldo_sep';
                    var title = 'September';
                }else if(config.dataPointIndex == 9) {
                    var filter = 'saldo_oct';
                    var title = 'October';
                }else if(config.dataPointIndex == 10) {
                    var filter = 'saldo_nov';
                    var title = 'November';
                }else if(config.dataPointIndex ==11) {
                    var filter = 'saldo_dec';
                    var title = 'December';
                }
                
                var tahun = <?= $tahun = date("Y"); ?>

                console.log(filter);
                // console.log(tahun);
                $.ajax({
                    type : 'post',
                    url : '../dashboard/detail_cash_on_hand.php',
                    data : {'filter': filter},
                    success : function(data){
                        $('#detail_coh').html(data);
                        $('#jdl_coh').html(title);
                        $('#modaldetcoh').modal('show');
                    },
                    error:  function (xhr, ajaxOptions, thrownError) {
                     console.log(xhr);
                 }
             });         

            }
        }
    },
    colors: ['#008B8B'],
},
plotOptions: {
  bar: {
    borderRadius: 5,
    dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartcoh"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M");
              $tahun = date("Y"); 

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01','2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01','1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloantotal"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.02')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanusd"), options);
chart.render();
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $tahun = date("Y");

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo_jan,',',saldo_feb,',',saldo_mar,',',saldo_apr,',',saldo_may,',',saldo_jun,',',saldo_jul,',',saldo_aug,',',saldo_sep,',',saldo_oct,',',saldo_nov,',',saldo_dec) data from (select round(abs(sum(saldo_jan /1000000)),2) saldo_jan, round(abs(sum(saldo_feb /1000000)),2) saldo_feb, round(abs(sum(saldo_mar /1000000)),2) saldo_mar, round(abs(sum(saldo_apr /1000000)),2) saldo_apr, round(abs(sum(saldo_may /1000000)),2) saldo_may, round(abs(sum(saldo_jun /1000000)),2) saldo_jun, round(abs(sum(saldo_jul /1000000)),2) saldo_jul, round(abs(sum(saldo_aug /1000000)),2) saldo_aug, round(abs(sum(saldo_sep /1000000)),2) saldo_sep, round(abs(sum(saldo_oct /1000000)),2) saldo_oct, round(abs(sum(saldo_nov /1000000)),2) saldo_nov, round(abs(sum(saldo_dec /1000000)),2) saldo_dec  from (select  saldo_jan, saldo_feb, saldo_mar, saldo_apr, saldo_may, saldo_jun, saldo_jul, saldo_aug, saldo_sep, saldo_oct, saldo_nov, saldo_dec from b_trial_balance_$tahun where no_coa IN ('2.20.01')
                UNION
                select if (saldo_jan < 0, saldo_jan, 0) saldo_jan, if (saldo_feb < 0, saldo_feb, 0) saldo_feb, if (saldo_mar < 0, saldo_mar, 0) saldo_mar, if (saldo_apr < 0, saldo_apr, 0) saldo_apr, if (saldo_may < 0, saldo_may, 0) saldo_may, if (saldo_jun < 0, saldo_jun, 0) saldo_jun, if (saldo_jul < 0, saldo_jul, 0) saldo_jul, if (saldo_aug < 0, saldo_aug, 0) saldo_aug, if (saldo_sep < 0, saldo_sep, 0) saldo_sep, if (saldo_oct < 0, saldo_oct, 0) saldo_oct, if (saldo_nov < 0, saldo_nov, 0) saldo_nov, if (saldo_dec < 0, saldo_dec, 0) saldo_dec  from b_trial_balance_$tahun where no_coa IN ('1.10.01')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',nama,'''') nama from (
        select CONCAT('Jan ',YEAR(CURRENT_DATE())) nama
        UNION
        select CONCAT('Feb ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Mar ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Apr ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('May ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jun ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Jul ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Aug ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Sep ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Oct ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Nov ',YEAR(CURRENT_DATE()))
        UNION
        select CONCAT('Dec ',YEAR(CURRENT_DATE()))) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $nama = isset($row_bln['nama']) ? $row_bln['nama'] :''; 
      echo $nama;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartloanidr"), options);
chart.render();
</script>

<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $tahun = date("Y");
        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bli = (abs($total_bli) / $limit_idr) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bli ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>

<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $sql_fil = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('round(abs(sum(saldo1 /1000000)),2) saldo1') filter
                UNION
                select CONCAT('round(abs(sum(saldo2 /1000000)),2) saldo2')
                UNION
                select CONCAT('round(abs(sum(saldo3 /1000000)),2) saldo3')) a");
              $row_fil = mysqli_fetch_array($sql_fil);
              $filter = isset($row_fil['filter']) ? $row_fil['filter'] :0;

              $sql_fila = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' saldo1') filter
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),' saldo2')
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),' saldo3')) a");
              $row_fila = mysqli_fetch_array($sql_fila);
              $filtera = isset($row_fila['filter']) ? $row_fila['filter'] :0;

              $sql_filb = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),',0) saldo1') filter
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),',0) saldo2')
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),',0) saldo3')) a");
              $row_filb = mysqli_fetch_array($sql_filb);
              $filterb = isset($row_filb['filter']) ? $row_filb['filter'] :0;

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo1,',',saldo2,',',saldo3) data from (select $filter from (select $filtera from b_trial_balance_2024 where no_coa IN ('2.20.01')
                UNION
                select $filterb from b_trial_balance_2024 where no_coa IN ('1.10.01')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',bulan,'''') bulan from (
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%b %Y') bulan
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH), '%b %Y')
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), '%b %Y')) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $bulan = isset($row_bln['bulan']) ? $row_bln['bulan'] :''; 
      echo $bulan;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv2"), options);
chart.render();
</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv3");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        // $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        // $row_blu = mysqli_fetch_array($sql_blu);
        // $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        // $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        $chart_blu = (abs($saldoakhir * $rates3) / $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_blu ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>
<!-- select CONCAT('round(abs(sum(saldo2 /1000000)),2) saldo2') -->
<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $sql_fil = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('round(abs(sum(saldo1 /1000000)),2) saldo1') filter
                UNION
                select CONCAT('round(abs(sum(0 /1000000)),2) saldo2')
                UNION
                select CONCAT('round(abs(sum(saldo3 /1000000)),2) saldo3')) a");
              $row_fil = mysqli_fetch_array($sql_fil);
              $filter = isset($row_fil['filter']) ? $row_fil['filter'] :0;

              $sql_fila = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' saldo1') filter
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),' saldo2')
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),' saldo3')) a");
              $row_fila = mysqli_fetch_array($sql_fila);
              $filtera = isset($row_fila['filter']) ? $row_fila['filter'] :0;

              $sql_filb = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),',0) saldo1') filter
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),',0) saldo2')
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),',0) saldo3')) a");
              $row_filb = mysqli_fetch_array($sql_filb);
              $filterb = isset($row_filb['filter']) ? $row_filb['filter'] :0;

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo1,',',saldo2,',',saldo3) data from (select $filter from (select $filtera from b_trial_balance_2024 where no_coa IN ('2.20.02')
                UNION
                select $filterb from b_trial_balance_2024 where no_coa IN ('1.10.02')) a) a");

              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',bulan,'''') bulan from (
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%b %Y') bulan
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH), '%b %Y')
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), '%b %Y')) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $bulan = isset($row_bln['bulan']) ? $row_bln['bulan'] :''; 
      echo $bulan;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv4"), options);
chart.render();
</script>


<script>
    am5.ready(function() {

// Create root element
// https://www.amcharts.com/docs/v5/getting-started/#Root_element
        var root = am5.Root.new("chartdiv5");


// Set themes
// https://www.amcharts.com/docs/v5/concepts/themes/
        root.setThemes([
          am5themes_Animated.new(root)
          ]);


// Create chart
// https://www.amcharts.com/docs/v5/charts/radar-chart/
        var chart = root.container.children.push(am5radar.RadarChart.new(root, {
          panX: false,
          panY: false,
          startAngle: 170,
          endAngle: 370
      }));


// Create axis and its renderer
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Axes
        var axisRenderer = am5radar.AxisRendererCircular.new(root, {
          innerRadius: -40
      });

        axisRenderer.grid.template.setAll({
          stroke: root.interfaceColors.get("background"),
          visible: true,
          strokeOpacity: 0
      });

        var xAxis = chart.xAxes.push(am5xy.ValueAxis.new(root, {
          maxDeviation: 0,
          min: 0,
          max: 100,
          strictMinMax: true,
          renderer: axisRenderer
      }));


// Add clock hand
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Clock_hands
        var axisDataItem = xAxis.makeDataItem({});

        var clockHand = am5radar.ClockHand.new(root, {
          pinRadius: am5.percent(15),
          radius: am5.percent(100),
          bottomWidth: 40
      })

        var bullet = axisDataItem.set("bullet", am5xy.AxisBullet.new(root, {
          sprite: clockHand
      }));

        xAxis.createAxisRange(axisDataItem);

        var label = chart.radarContainer.children.push(am5.Label.new(root, {
          fill: am5.color(0xffffff),
          centerX: am5.percent(50),
          textAlign: "center",
          centerY: am5.percent(50),
          fontSize: "1.2em"
      }));

        axisDataItem.set("value", 0);
        bullet.get("sprite").on("rotation", function () {
          var value = axisDataItem.get("value");
          var text = Math.round(axisDataItem.get("value")).toString();
          var fill = am5.color(0x000000);
          xAxis.axisRanges.each(function (axisRange) {
            if (value >= axisRange.get("value") && value <= axisRange.get("endValue")) {
              fill = axisRange.get("axisFill").get("fill");
          }
      })

          label.set("text", Math.round(value).toString());

          clockHand.pin.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
          clockHand.hand.animate({ key: "fill", to: fill, duration: 500, easing: am5.ease.out(am5.ease.cubic) })
      });

        <?php 
        $bulan = date("M"); 
        $sql_blu = mysqli_query($conn2,"select total,(total * rate) total_convert from (select no_coa,nama_coa,round(sum(total),0) total from (select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.02')
            UNION
            select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.02')) a) a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b");
        $row_blu = mysqli_fetch_array($sql_blu);
        $total_blu = isset($row_blu['total']) ? $row_blu['total'] :0;
        $total_convert_blu = isset($row_blu['total_convert']) ? $row_blu['total_convert'] :0;

        $sql1 = mysqli_query($conn2,"select fac_limit,(fac_limit * rate) limit_convert from (select SUM(fac_limit) fac_limit from b_masterbank where curr = 'usd') a join (select COALESCE(rate,1) rate from masterrate where tanggal = CURRENT_DATE() and v_codecurr = 'PAJAK') b ");
        $row1 = mysqli_fetch_array($sql1);
        $fac_limit = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;
        $limit_convert = isset($row1['limit_convert']) ? $row1['limit_convert'] :0;

        // $sql_bli = mysqli_query($conn2,"select no_coa,nama_coa,round(- sum(total),0) total from(select no_coa,nama_coa,saldo_$bulan total from b_trial_balance_2025 where no_coa IN ('2.20.01')
        //     UNION
        //     select no_coa,nama_coa,if(saldo_$bulan < 0,saldo_$bulan,0) total from b_trial_balance_2025 where no_coa IN ('1.10.01')) a");
        // $row_bli = mysqli_fetch_array($sql_bli);
        // $total_bli = isset($row_bli['total']) ? $row_bli['total'] :0;

        $sql1 = mysqli_query($conn2,"select SUM(fac_limit) fac_limit from b_masterbank where curr = 'IDR'");
        $row1 = mysqli_fetch_array($sql1);
        $limit_idr = isset($row1['fac_limit']) ? $row1['fac_limit'] :0;

        $chart_bl = (abs($total_bli) + abs($saldoakhir * $rates3)) / ($limit_idr + $limit_convert) * 100;

        ?>

        setInterval(function () {
          axisDataItem.animate({
            key: "value",
            to: <?= $chart_bl ?>,
            duration: 500,
            easing: am5.ease.out(am5.ease.cubic)
        });
      }, 2000)

        chart.bulletsContainer.set("mask", undefined);


// Create axis ranges bands
// https://www.amcharts.com/docs/v5/charts/radar-chart/gauge-charts/#Bands
        var bandsData = [{
          title: "Low",
          color: "#54b947",
          lowScore: 0,
          highScore: 25
      }, {
          title: "Medium",
          color: "#fdae19",
          lowScore: 25,
          highScore: 75
      }, {
          title: "High",
          color: "#FA8072",
          lowScore: 75,
          highScore: 100
      }];

        am5.array.each(bandsData, function (data) {
          var axisRange = xAxis.createAxisRange(xAxis.makeDataItem({}));

          axisRange.setAll({
            value: data.lowScore,
            endValue: data.highScore
        });

          axisRange.get("axisFill").setAll({
            visible: true,
            fill: am5.color(data.color),
            fillOpacity: 0.8
        });

          axisRange.get("label").setAll({
            text: data.title,
            inside: true,
            radius: 15,
            fontSize: "0.9em",
            fill: root.interfaceColors.get("background")
        });
      });


// Make stuff animate on load
        chart.appear(1000, 100);

}); // end am5.ready()
</script>


<script>
    var options = {
      series: [{
          name: 'Bank Loan',
          data: [<?php 
              $bulan = date("M"); 
              $sql_fil = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('round(abs(sum(saldo1 /1000000)),2) saldo1') filter
                UNION
                select CONCAT('round(abs(sum(saldo2 /1000000)),2) saldo2')
                UNION
                select CONCAT('round(abs(sum(saldo3 /1000000)),2) saldo3')) a");
              $row_fil = mysqli_fetch_array($sql_fil);
              $filter = isset($row_fil['filter']) ? $row_fil['filter'] :0;

              $sql_fila = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' saldo1') filter
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),' saldo2')
                UNION
                select CONCAT('saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),' saldo3')) a");
              $row_fila = mysqli_fetch_array($sql_fila);
              $filtera = isset($row_fila['filter']) ? $row_fila['filter'] :0;

              $sql_filb = mysqli_query($conn2,"select GROUP_CONCAT(filter) filter from (
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),',0) saldo1') filter
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 2 MONTH),'%b'),',0) saldo2')
                UNION
                select CONCAT('if(saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 3 MONTH),'%b'),' < 0, saldo_',DATE_FORMAT(DATE_SUB(CURRENT_DATE,INTERVAL 1 MONTH),'%b'),',0) saldo3')) a");
              $row_filb = mysqli_fetch_array($sql_filb);
              $filterb = isset($row_filb['filter']) ? $row_filb['filter'] :0;

              $sql1 = mysqli_query($conn2,"select CONCAT(saldo1,',',saldo2,',',saldo3) data from (select $filter from (select $filtera from b_trial_balance_2024 where no_coa IN ('2.20.01','2.20.02')
                UNION
                select $filterb from b_trial_balance_2024 where no_coa IN ('1.10.01','1.10.02')) a) a");
              $row1 = mysqli_fetch_array($sql1);
              $data_bar1 = isset($row1['data']) ? $row1['data'] :0;
              echo $data_bar1;

              ?>]
      }],
      chart: {
          height: 350,
          type: 'bar',
          colors: ['#008B8B'],
      },
      plotOptions: {
          bar: {
            borderRadius: 5,
            dataLabels: {
              position: 'top', // top, center, bottom
          },
      }
  },
  dataLabels: {
      enabled: true,
      formatter: function (val) {
        return val.toLocaleString('en-US');
    },
    offsetY: -20,
    style: {
        fontSize: '12px',
        colors: ["#304758"]
    }
},

xaxis: {
  categories: [<?php
      $sql_bln = mysqli_query($conn2,"select GROUP_CONCAT('''',bulan,'''') bulan from (
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 3 MONTH), '%b %Y') bulan
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH), '%b %Y')
        UNION
        select DATE_FORMAT(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH), '%b %Y')) a");
      $row_bln = mysqli_fetch_array($sql_bln);
      $bulan = isset($row_bln['bulan']) ? $row_bln['bulan'] :''; 
      echo $bulan;
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
}
},
yaxis: {
  axisBorder: {
    show: false
},
axisTicks: {
    show: false,
    colors: ["#304758"]
},
labels: {
    show: false,
    formatter: function (val) {
              // return val + "%";
      return val.toLocaleString('en-US');
  }
}

},
title: {
  text: '',
  floating: true,
  offsetY: 330,
  align: 'center',
  style: {
    color: '#444'
}
}
};

var chart = new ApexCharts(document.querySelector("#chartdiv6"), options);
chart.render();
</script>