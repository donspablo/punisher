<?php

class RawOutput extends Output
{

    protected function sendHeaders()
    {
        header('Content-Type: text/plain; charset="utf-8"');
        header('Content-Disposition: inline; filename=""');
    }

}