
<?php

require_once('AbstractResult.php');

class Errorr extends AbstractResult
{
    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        if (!$this->getData('error_message')) {
            $this->setData(
                'error_message',
                __('Este método no esta habilitado. Para utilizar este método por favor ponte en contacto con nosotros.')
            );
        }
        return $this->getData('error_message');
    }
}