<?php // Tab Summary - payable_card_statement2.php ?>
<div class="d-flex justify-content-start mb-3">
  <a id="btnExportExcel_sum_grup" target="_blank">
    <button type="button" class="btn btn-success btn-sm pcs2-btn-excel">
      <i class="fa fa-file-excel-o"></i> Excel
    </button>
  </a>
</div>

<div class="pcs2-table-wrap" style="overflow-x:auto;">
<table id="table-pcs-sum-grup"
class="table table-striped table-hover table-sm nowrap pcs2-table"
style="min-width:2200px;table-layout:fixed;font-size:12px;">
<colgroup>
    <col style="width:260px"><col style="width:90px"><col style="width:130px"><col style="width:150px"><col style="width:50px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:130px">
    <col style="width:50px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:130px">
</colgroup>
<thead>
<tr>
    <th rowspan="2" class="grp-head-default">Supplier Category &amp; Name</th>
    <th colspan="3" class="grp-head-default" style="text-align:center;">Amount</th>
    <th rowspan="2" class="grp-head-spacer"></th>
    <th colspan="9" class="grp-head-aging">Account Payable Aging Based on Due Date</th>
    <th rowspan="2" class="grp-head-spacer"></th>
    <th colspan="8" class="grp-head-projection">Account Payable Based on Due Date Projection</th>
</tr>
<tr>
    <th class="grp-head-default">Currency</th>
    <th class="grp-head-default">Foreign Currency</th>
    <th class="grp-head-default">Equivalent IDR</th>
    <th class="grp-head-aging">Current</th>
    <th class="grp-head-aging">1-30</th>
    <th class="grp-head-aging">31-60</th>
    <th class="grp-head-aging">61-90</th>
    <th class="grp-head-aging">91-120</th>
    <th class="grp-head-aging">121-180</th>
    <th class="grp-head-aging">181-360</th>
    <th class="grp-head-aging">&gt;360</th>
    <th class="grp-head-aging">Total</th>
    <th class="grp-head-projection">Due</th>
    <th id="proj-month-sum-grup-1" class="grp-head-projection"></th>
    <th id="proj-month-sum-grup-2" class="grp-head-projection"></th>
    <th id="proj-month-sum-grup-3" class="grp-head-projection"></th>
    <th id="proj-month-sum-grup-4" class="grp-head-projection"></th>
    <th id="proj-month-sum-grup-5" class="grp-head-projection"></th>
    <th id="proj-month-sum-grup-6" class="grp-head-projection"></th>
    <th class="grp-head-projection">Total</th>
</tr>
<tr>
    <th colspan="4" style="background:white;text-align:left;color:#333;">GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
</thead>
<tbody></tbody>
<tfoot>
    <tr class="pcs2-foot-idr"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-usd"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-all"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
</tfoot>
</table>

<table id="table-pcs-sum-non-grup"
class="table table-striped table-hover table-sm nowrap pcs2-table"
style="min-width:2200px;table-layout:fixed;margin-top:-1px;font-size:12px;">
<colgroup>
    <col style="width:260px"><col style="width:90px"><col style="width:130px"><col style="width:150px"><col style="width:50px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:130px">
    <col style="width:50px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:120px">
    <col style="width:120px"><col style="width:120px"><col style="width:120px"><col style="width:130px">
</colgroup>
<thead>
<tr>
    <th colspan="4" style="background:white;text-align:left;color:#333;">NON GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
</thead>
<tbody></tbody>
<tfoot>
    <tr class="pcs2-foot-idr"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-usd"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-all"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <!-- tr:eq(3) separator kosong, tr:eq(4..6) grand summary (GROUP+NON GROUP) -->
    <tr><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-idr"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-usd"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
    <tr class="pcs2-foot-all"><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th></tr>
</tfoot>
</table>
</div>
