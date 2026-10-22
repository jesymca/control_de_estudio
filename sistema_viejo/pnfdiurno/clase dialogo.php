<?php
class dialogo
{
    private $_fechaNacimiento;
    /**
     *
     * @param string $fechaNacimiento 5/8/1973
     */
    public function __construct($fechaNacimiento)
    {
        $this->_fechaNacimiento=$fechaNacimiento;
    }

    public function decirEdad()
    {
        return $this->_calcularEdad();
    }

}


$dialogo=new dialogo('19/02/1970');
echo $dialogo->decirEdad();