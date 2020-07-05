<?php


namespace Espo\Core\Utils\Api;

class Output
{
    private $slim;

    protected $errorDescriptions = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        403 => 'Forbidden',
        404 => 'Page Not Found',
        409 => 'Conflict',
        500 => 'Internal Server Error',
    ];

    protected $allowedStatusCodeList = [
        200, 201, 400, 401, 403, 404, 409, 500
    ];

    protected $ignorePrintXStatusReasonExceptionClassNameList = [
        'PDOException'
    ];

    public function __construct(\Espo\Core\Utils\Api\Slim $slim)
    {
        $this->slim = $slim;
    }

    protected function getSlim()
    {
        return $this->slim;
    }

    public function render($data = null)
    {
        if (is_array($data)) {
            $dataArr = array_values($data);
            $data = empty($dataArr[0]) ? false : $dataArr[0];
        }

        ob_clean();
        echo $data;
    }

    public function processError(string $message = 'Error', int $code = 500, bool $toPrint = false, $exception = null)
    {
        $currentRoute = $this->getSlim()->router()->getCurrentRoute();

        if (isset($currentRoute)) {
            $inputData = $this->getSlim()->request()->getBody();
            $inputData = $this->clearPasswords($inputData);
            $GLOBALS['log']->error('API ['.$this->getSlim()->request()->getMethod().']:'.$currentRoute->getPattern().', Params:'.print_r($currentRoute->getParams(), true).', InputData: '.$inputData.' - '.$message);
        }

        $this->displayError($message, $code, $toPrint, $exception);
    }

    public function displayError(string $text, int $statusCode = 500, bool $toPrint = false, $exception = null)
    {
        $GLOBALS['log']->error('Display Error: '.$text.', Code: '.$statusCode.' URL: '.$_SERVER['REQUEST_URI']);

        ob_clean();

        if (!empty($this->slim)) {
            $toPrintXStatusReason = true;
            if ($exception && in_array(get_class($exception), $this->ignorePrintXStatusReasonExceptionClassNameList)) {
                $toPrintXStatusReason = false;
            }

            if (!in_array($statusCode, $this->allowedStatusCodeList)) {
                $statusCode = 500;
            }

            $this->getSlim()->response()->setStatus($statusCode);
            if ($toPrintXStatusReason) {
                $this->getSlim()->response()->headers->set('X-Status-Reason', $text);
            }

            if ($toPrint) {
                $status = $this->getCodeDescription($statusCode);
                $status = isset($status) ? $statusCode.' '.$status : 'HTTP '.$statusCode;
                $this->getSlim()->printError($text, $status);
            }

            $this->getSlim()->stop();
        } else {
            $GLOBALS['log']->info('Could not get Slim instance. It looks like a direct call (bypass API). URL: '.$_SERVER['REQUEST_URI']);
            die($text);
        }
    }

    protected function getCodeDescription($statusCode)
    {
        if (isset($this->errorDescriptions[$statusCode])) {
            return $this->errorDescriptions[$statusCode];
        }

        return null;
    }

    protected function clearPasswords($inputData)
    {
        return preg_replace('/"(.*?password.*?)":".*?"/i', '"$1":"*****"', $inputData);
    }
}
