<?php // Tab BPB - payable_card_statement2.php (copy dari ap_report/pcs_bpb.php, styling dimodernisasi) ?>
<div class="d-flex justify-content-start mb-3">
  <a id="btnExportExcel_bpb" target="_blank">
    <button type="button" class="btn btn-success btn-sm pcs2-btn-excel">
      <i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel
    </button>
  </a>
</div>

<div class="pcs2-table-wrap">
  <table id="table-pcs-bpb"
  class="table table-striped table-hover table-sm nowrap pcs2-table">
    <thead>
      <tr>
        <th rowspan="2" class="grp-head-default">Nama Supplier</th>
        <th rowspan="2" class="grp-head-default">Bpb Number</th>
        <th rowspan="2" class="grp-head-default">Bpb Date</th>
        <th rowspan="2" class="grp-head-default">Due Date</th>
        <th rowspan="2" class="grp-head-default">Currency</th>
        <th rowspan="2" class="grp-head-default">Begining Balance</th>
        <th rowspan="2" class="grp-head-default">Addition</th>
        <th rowspan="2" class="grp-head-default">Reverse BPB</th>
        <th rowspan="2" class="grp-head-default">Deduction KB</th>
        <th rowspan="2" class="grp-head-default">Reverse KB</th>
        <th rowspan="2" class="grp-head-default">Deduction GM</th>
        <th rowspan="2" class="grp-head-default">Ending Balance</th>
        <th rowspan="2" class="grp-head-default">Rate</th>
        <th rowspan="2" class="grp-head-default">Ending Balance IDR</th>
        <th rowspan="2" class="grp-head-default">COA No</th>
        <th rowspan="2" class="grp-head-default">COA Name</th>
        <th rowspan="2" class="grp-head-accent1">Item Type 1</th>
        <th rowspan="2" class="grp-head-accent1">Item Type 2</th>
        <th rowspan="2" class="grp-head-accent1">Relationship</th>
        <th rowspan="2" class="grp-head-spacer"></th>
        <th colspan="9" class="grp-head-aging">Account Payable Aging Based on Due Date</th>
        <th rowspan="2" class="grp-head-spacer"></th>
        <th colspan="8" id="thProjection" class="grp-head-projection">Account Payable Based on Due Date Projection</th>
      </tr>
      <tr>
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
        <th id="proj-month-1" class="grp-head-projection"></th>
        <th id="proj-month-2" class="grp-head-projection"></th>
        <th id="proj-month-3" class="grp-head-projection"></th>
        <th id="proj-month-4" class="grp-head-projection"></th>
        <th id="proj-month-5" class="grp-head-projection"></th>
        <th id="proj-month-6" class="grp-head-projection"></th>
        <th class="grp-head-projection">Total</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
    <tfoot>
      <tr class="pcs2-foot-idr">
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th>
      </tr>
      <tr class="pcs2-foot-usd">
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th>
      </tr>
      <tr class="pcs2-foot-all">
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th>
        <th></th>
        <th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th>
      </tr>
    </tfoot>
  </table>
</div>
