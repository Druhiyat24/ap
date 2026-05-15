<div class="col-md-3 d-flex align-items-end">

    <a id="btnExportExcel_bpb" target="_blank">
    <button type="button" class="btn btn-success btn-xs ml-2" style="margin-top: 30px;">
        <i class="fa fa-file-excel-o" aria-hidden="true" > Excel</i>
    </button>
</a>
</div>

    <div class="card-body p-4">
      <div class="table-responsive">
          <table id="table-pcs-bpb" 
          class="table table-striped table-bordered table-hover table-sm nowrap" >
          <thead class="table-gradient">
            <tr>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Nama Supplier</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Number</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Bpb Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Due Date</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Currency</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Begining Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Addition</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Reverse BPB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction KB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Reverse KB</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Deduction GM</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Rate</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">Ending Balance IDR</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA No</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFE4E1;">COA Name</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 1</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Item Type 2</th>
                <th rowspan="2" style="text-align: center;vertical-align: middle;background-color: #FFDAB9;">Relationship</th> 
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th>    
                <th colspan="9" style="text-align: center;vertical-align: middle;background-color: #98FB98;">Account Payable Aging Based on Due Date</th>
                <th rowspan="2" style="border: none;width: 50px;background-color: white;"></th> 
                <th colspan="8" id="thProjection"  style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Account Payable Based on Due Date Projection</th>
            </tr>
            <tr>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Current</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">1-30</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">31-60</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">61-90</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">91-120</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">121-180</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">181-360</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">>360</th>
                <th style="text-align: center;vertical-align: middle;background-color: #98FB98;">Total</th>
                <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Due</th>
                <th id="proj-month-1" style="background:#87CEFA;"></th>
                <th id="proj-month-2" style="background:#87CEFA;"></th>
                <th id="proj-month-3" style="background:#87CEFA;"></th>
                <th id="proj-month-4" style="background:#87CEFA;"></th>
                <th id="proj-month-5" style="background:#87CEFA;"></th>
                <th id="proj-month-6" style="background:#87CEFA;"></th>
                <th style="text-align: center;vertical-align: middle;background-color: #87CEFA;">Total</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
        <tr>
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
        <tr>
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
        <tr>
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
</div>