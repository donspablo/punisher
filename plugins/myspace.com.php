<?php


function preParse($input, $type)
{

    switch ($type) {

        case 'html':


            $input = str_replace('"invalidLogin.innerHTML = \""', '"invalidLogin.in"+"nerHTML = \""', $input);

            $insert = <<<OUT
				<script type="text/javascript">
				XMLHttpRequest.prototype.open = function(method,uri,async) {
					return this.base_open(method, parseURL(uri.replace('localhost', 'www.myspace.com'), 'ajax'), async);
				};
				</script>
OUT;
            $input = str_replace('</head>', $insert . '</head>', $input);

            break;

    }

    return $input;

}
