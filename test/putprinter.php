<?php parse_str(file_get_contents("php://input"), $putVars); echo implode(',', $putVars); ?>
