<!-- ================== EXPORT BUTTON ================== -->
<div class="col-md-3 d-flex align-items-end">
    <a id="btnExportExcel_sum_grup" target="_blank">
        <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top:30px;">
            <i class="fa fa-file-excel-o" aria-hidden="true"> Excel</i>
        </button>
    </a>
</div>

<!-- ================== TABLE WRAPPER ================== -->
<div class="card-body p-4">
<div class="table-responsive" style="max-width:100%;overflow-x:auto;">

<!-- ================== TABLE GROUP ================== -->
<table id="table-pcs-sum-grup"
class="table table-striped table-bordered table-hover table-sm nowrap"
style="min-width:2200px;table-layout:fixed;font-size: 12px;">

<!-- ===== COLGROUP (WAJIB & SAMA DENGAN TABLE BAWAH) ===== -->
<colgroup>
    <col style="width:260px">
    <col style="width:90px">
    <col style="width:130px">
    <col style="width:150px">
    <col style="width:50px">

    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:130px">

    <col style="width:50px">

    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:130px">
</colgroup>

<thead class="table-gradient">
<tr>
    <th rowspan="2" style="text-align:center;vertical-align:middle;background-color:#FFE4E1;">
        Supplier Category & Name
    </th>
    <th colspan="3" style="text-align:center;vertical-align:middle;background-color:#FFE4E1;">Amount</th>
    <th rowspan="2" style="border:none;width:50px;background-color:white;"></th>
    <th colspan="9" style="text-align:center;vertical-align:middle;background-color:#98FB98;">
        Account Payable Aging Based on Due Date
    </th>
    <th rowspan="2" style="border:none;width:50px;background-color:white;"></th>
    <th colspan="8" style="text-align:center;vertical-align:middle;background-color:#87CEFA;">
        Account Payable Based on Due Date Projection
    </th>
</tr>

<tr>
    <th style="text-align:center;vertical-align:middle;background-color:#FFE4E1;">Currency</th>
    <th style="text-align:center;vertical-align:middle;background-color:#FFE4E1;">Foreign Currency</th>
    <th style="text-align:center;vertical-align:middle;background-color:#FFE4E1;">Equivalent IDR</th>

    <th style="background-color:#98FB98;">Current</th>
    <th style="background-color:#98FB98;">1-30</th>
    <th style="background-color:#98FB98;">31-60</th>
    <th style="background-color:#98FB98;">61-90</th>
    <th style="background-color:#98FB98;">91-120</th>
    <th style="background-color:#98FB98;">121-180</th>
    <th style="background-color:#98FB98;">181-360</th>
    <th style="background-color:#98FB98;">&gt;360</th>
    <th style="background-color:#98FB98;">Total</th>

    <th style="background-color:#87CEFA;">Due</th>
    <th id="proj-month-sum-grup-1" style="background:#87CEFA;"></th>
    <th id="proj-month-sum-grup-2" style="background:#87CEFA;"></th>
    <th id="proj-month-sum-grup-3" style="background:#87CEFA;"></th>
    <th id="proj-month-sum-grup-4" style="background:#87CEFA;"></th>
    <th id="proj-month-sum-grup-5" style="background:#87CEFA;"></th>
    <th id="proj-month-sum-grup-6" style="background:#87CEFA;"></th>
    <th style="background-color:#87CEFA;">Total</th>
</tr>

<tr>
    <th colspan="4" style="background:white;text-align:left;">GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
</thead>

<tbody></tbody>
<tfoot>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
    </tfoot>
</table>

<!-- ================== TABLE NON GROUP ================== -->
<table id="table-pcs-sum-non-grup"
class="table table-striped table-bordered table-hover table-sm nowrap"
style="min-width:2200px;table-layout:fixed;margin-top:-1px;font-size: 12px;">

<!-- ===== COLGROUP (HARUS IDENTIK) ===== -->
<colgroup>
    <col style="width:260px">
    <col style="width:90px">
    <col style="width:130px">
    <col style="width:150px">
    <col style="width:50px">

    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:130px">

    <col style="width:50px">

    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:120px">
    <col style="width:130px">
</colgroup>

<thead>
<tr>
    <th colspan="4" style="background:white;text-align:left;">NON GROUP</th>
    <th style="border:none;background:white;"></th>
    <th colspan="9" style="background:white;"></th>
    <th style="border:none;background:white;"></th>
    <th colspan="8" style="background:white;"></th>
</tr>
</thead>

<tbody></tbody>
<tfoot>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>

        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
        <tr>
            <th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
            <th></th><th></th><th></th><th></th><th></th>
        </tr>
    </tfoot>
</table>


</div>
</div>

<!-- ================== CSS PENGUNCI ================== -->
<style>
#table-pcs-sum-grup,
#table-pcs-sum-non-grup{
    table-layout:fixed !important;
}
#table-pcs-sum-grup th,
#table-pcs-sum-grup td,
#table-pcs-sum-non-grup th,
#table-pcs-sum-non-grup td{
    white-space:nowrap;
    font-size:12px;
}
</style>
