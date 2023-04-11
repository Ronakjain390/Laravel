<?php
$data = generalSetting();
?>
 @if(isset($data->copyright_text))
<footer id="footer" class="footer">
    <div class="copyright">
        &copy; {{$data->copyright_text}} <strong><span></span></strong>
    </div>
</footer>
@endif
<a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>