//get ip client
<input type="text" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
echo $_SERVER['REMOTE_ADDR'];
?>" >

//get komputer name client
<input type="text" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
echo gethostbyaddr($_SERVER['REMOTE_ADDR']);
?>" >

//get ip & komputer name client
<input type="text" style="font-size: 12px;" class="form-control" id="ambil_ip" name="ambil_ip" 
            value="<?php
echo gethostbyaddr($_SERVER['REMOTE_ADDR']) .' '.$_SERVER['REMOTE_ADDR'];
?>" >
