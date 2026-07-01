<?php // Tab Item Type 1 - payable_card_statement2.php ?>
<div class="d-flex justify-content-start mb-3">
  <a id="btnExportExcel_type1" target="_blank">
    <button type="button" class="btn btn-success btn-sm pcs2-btn-excel">
      <i class="fa fa-file-excel-o"></i> Excel
    </button>
  </a>
</div>

<div class="pcs2-table-wrap">
  <table id="table-pcs-type1"
  class="table table-striped table-hover table-sm nowrap pcs2-table">
    <thead>
      <tr>
        <th rowspan="2" class="grp-head-default">Nama Supplier</th>
        <th rowspan="2" class="grp-head-default">Item Type</th>
        <th rowspan="2" class="grp-head-default">Relationship</th>
        <th rowspan="2" class="grp-head-default">Amount (Equivalent IDR)</th>
        <th rowspan="2" class="grp-head-default">Percentage from Total</th>
        <th rowspan="2" class="grp-head-spacer"></th>
        <th colspan="9" class="grp-head-aging">Account Payable Aging Based on Due Date</th>
        <th rowspan="2" class="grp-head-spacer"></th>
        <th colspan="8" id="thProjection-type1" class="grp-head-projection">Account Payable Based on Due Date Projection</th>
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
        <th id="proj-month-type1-1" class="grp-head-projection"></th>
        <th id="proj-month-type1-2" class="grp-head-projection"></th>
        <th id="proj-month-type1-3" class="grp-head-projection"></th>
        <th id="proj-month-type1-4" class="grp-head-projection"></th>
        <th id="proj-month-type1-5" class="grp-head-projection"></th>
        <th id="proj-month-type1-6" class="grp-head-projection"></th>
        <th class="grp-head-projection">Total</th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th></th><th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
        <th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th><th></th>
      </tr>
    </tfoot>
  </table>
</div>
