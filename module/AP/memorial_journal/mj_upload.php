<form id="form-data3" method="post">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="form-row align-items-end">

    <div class="col-md-4 mb-3">
    <label><b>Upload File</b></label>

    <div style="position:relative;">

        <!-- BUTTON UPLOAD -->
        <button type="button" id="btnUpload"
            class="btn btn-primary btn-sm"
            style="
                position:absolute;
                top:10px;
                right:10px;
                border-radius:8px;
                padding:5px 12px;
                z-index:2;
            ">
            <i class="fa fa-upload"></i> Upload
        </button>

        <!-- BUTTON FORMAT -->
        <a target="_blank" href="format-excel/format_upload.xls?ver=<?= time(); ?>"
            style="
                position:absolute;
                top:45px;
                right:10px;
                z-index:2;
            ">
            <button type="button"
                class="btn btn-warning btn-sm"
                style="border-radius:8px;padding:5px 12px;">
                <i class="fa fa-file-excel-o"></i> Format
            </button>
        </a>

        <!-- BOX -->
        <label for="fileUpload" id="uploadBox" style="
            display:block;
            border:2px dashed #007bff;
            border-radius:12px;
            padding:50px 20px 60px 20px;
            text-align:center;
            cursor:pointer;
            background:#f8f9fa;
            transition:0.3s;
        ">
            <i class="fa fa-cloud-upload" style="font-size:35px;color:#007bff"></i>

            <div style="font-size:15px;margin-top:10px;font-weight:500">
                Klik atau drag file ke sini
            </div>

            <div id="fileName" style="font-size:12px;color:#666;margin-top:8px">
                Belum ada file
            </div>
        </label>

        <input type="file" id="fileUpload" style="display:none">

    </div>
</div>



</div>

            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="table-upload-mj"
                        class="table table-striped table-bordered table-hover table-sm">
                        <thead class="table-gradient">
                            <tr>
                                <th style="text-align: center;vertical-align: middle;">Profit Center</th>
                                <th style="text-align: center;vertical-align: middle;">COA</th>
                                <th style="text-align: center;vertical-align: middle;">Cost Center</th>
                                <th style="text-align: center;vertical-align: middle;">Reff Document</th>
                                <th style="text-align: center;vertical-align: middle;">Reff Date</th>
                                <th style="text-align: center;vertical-align: middle;">Buyer</th>
                                <th style="text-align: center;vertical-align: middle;">Worksheet</th>
                                <th style="text-align: center;vertical-align: middle;">Curr</th>
                                <th style="text-align: center;vertical-align: middle;">Debit</th>
                                <th style="text-align: center;vertical-align: middle;">Credit</th>
                                <th style="text-align: center;vertical-align: middle;">Description</th>
                                <th style="text-align: center;vertical-align: middle;">Include SB1</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-body p-2">
                <div class="table-responsive">
                    <table id="table-upload-mj-group"
                        class="table table-striped table-bordered table-hover table-sm">
                        <thead class="table-gradient">
                            <tr>
                                <th style="text-align: center;vertical-align: middle;">No Journal</th>
                                <th style="text-align: center;vertical-align: middle;">Journal Date</th>
                                <th style="text-align: center;vertical-align: middle;">Type Journal</th>
                                <th style="text-align: center;vertical-align: middle;">Curr</th>
                                <th style="text-align: center;vertical-align: middle;">Debit</th>
                                <th style="text-align: center;vertical-align: middle;">Credit</th>
                                <th style="text-align: center;vertical-align: middle;">Description</th>
                                <th style="text-align: center;vertical-align: middle;">Include SB1</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="row mt-1 p-3">

                <!-- NAG -->
                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Total Nirwana Alabare Garment</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_nag3" name="tot_debit_nag3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nag3" name="h_tot_debit_nag3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nag3" name="tot_credit_nag3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nag3" name="h_tot_credit_nag3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr_nag3" name="tot_debit_idr_nag3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr_nag3" name="h_tot_debit_idr_nag3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr_nag3" name="tot_credit_idr_nag3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr_nag3" name="h_tot_credit_idr_nag3" readonly>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- NAK -->
                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Total Nirwana Alabare Knitting</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_nak3" name="tot_debit_nak3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_nak3" name="h_tot_debit_nak3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_nak3" name="tot_credit_nak3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_nak3" name="h_tot_credit_nak3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr_nak3" name="tot_debit_idr_nak3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr_nak3" name="h_tot_debit_idr_nak3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr_nak3" name="tot_credit_idr_nak3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr_nak3" name="h_tot_credit_idr_nak3" readonly>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="col-md-4">
                    <div class="total-box">
                        <h6>Grand Total</h6>
                        <hr>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit3" name="tot_debit3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit3" name="h_tot_debit3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit3" name="tot_credit3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit3" name="h_tot_credit3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Debit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_debit_idr3" name="tot_debit_idr3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_debit_idr3" name="h_tot_debit_idr3" readonly>
                            </div>
                        </div>

                        <div class="row mb-3 align-items-center">
                            <div class="col-4">
                                <label class="mb-0">Total Credit IDR</label>
                            </div>
                            <div class="col-8">
                                <input type="text" style="font-size:14px;text-align:right"
                                    class="form-control" id="tot_credit_idr3" name="tot_credit_idr3" readonly>
                                <input type="hidden" style="font-size:14px;text-align:right"
                                    class="form-control" id="h_tot_credit_idr3" name="h_tot_credit_idr3" readonly>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="form-row">
                <div class="col-md-3 mt-3 mb-3">
                    <button type="button" style="border-radius: 6px" class="btn-outline-primary btn-sm" name="simpan3" id="simpan3"><span class="fa fa-floppy-o"></span> Save</button>
                    <button type="button" style="border-radius: 6px" class="btn-outline-danger btn-sm" name="batal" id="batal" onclick="location.href='memorial-journal.php'"><span class="fa fa-angle-double-left"></span> Back</button>
                </div>
            </div>
        </div>
    </div>
</form>