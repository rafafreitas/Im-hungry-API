<?php
/**
 * Created by PhpStorm.
 * User: Rafael Freitas
 * Date: 08/03/18
 * Time: 23:53
 */

require_once 'Basics/Empresa.php';
require_once 'Connection/Conexao.php';
class EmpresaDAO
{
    public function listAll(Empresa $empresa){

        $conn = \Database::conexao();
        $sql = "SELECT empresa_id, empresa_nome, empresa_telefone, empresa_cnpj, empresa_cep, empresa_lat, 
                       empresa_long, empresa_numero_endereco, empresa_complemento_endereco, empresa_data_fundacao, 
                       empresa_data_cadastro, empresa_foto_marca, empresa_foto_perfil, empresa_foto_capa, 
                       empresa_facebook, empresa_instagram, empresa_twitter, empresa_status, empresa_enabled, user_id
                FROM empresa 
                WHERE user_id = ?
                AND empresa_enabled = ?;";
        $stmt = $conn->prepare($sql);

        $enabled = ($empresa->getEnabled() == 'true')? true : false;

        try {
            $stmt->bindValue(1,$empresa->getUserId(), PDO::PARAM_INT);
            $stmt->bindValue(2,$enabled);
            $stmt->execute();
            $countLogin = $stmt->rowCount();
            $resultEmpresa = $stmt->fetchAll(PDO::FETCH_OBJ);

            if ($countLogin != 0) {
                return $resultEmpresa;
            }else{
                return array(
                    'status'    => 500,
                    'message'   => "INFO",
                    'qtd'       => 0,
                    'result'    => 'Você não possui empresas cadastradas!'
                );
            }

        } catch (PDOException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }

    }

    public function insert(Empresa $empresa){

        $conn = \Database::conexao();
        $sql = "INSERT INTO empresa (empresa_nome, empresa_telefone, empresa_cnpj, empresa_cep, empresa_lat, empresa_long,
                                     empresa_numero_endereco, empresa_complemento_endereco, empresa_data_fundacao, 
                                     empresa_data_cadastro, empresa_foto_marca, empresa_facebook, empresa_instagram, 
                                     empresa_twitter, empresa_status, user_id, empresa_enabled)
                VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, ?, ?, ?, TRUE, ?, 1);";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->bindValue(1,$empresa->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(2,$empresa->getTelefone(), PDO::PARAM_STR);
            $stmt->bindValue(3,$empresa->getCnpj(), PDO::PARAM_STR);
            $stmt->bindValue(4,$empresa->getCep(), PDO::PARAM_STR);
            $stmt->bindValue(5,$empresa->getLatitude(), PDO::PARAM_STR);
            $stmt->bindValue(6,$empresa->getLongitude(), PDO::PARAM_STR);
            $stmt->bindValue(7,$empresa->getNumeroEndereco(), PDO::PARAM_INT);
            $stmt->bindValue(8,$empresa->getComplementoEndereco(), PDO::PARAM_STR);
            $stmt->bindValue(9,$empresa->getDataFundacao(), PDO::PARAM_STR);
            $stmt->bindValue(10,$empresa->getFotoMarca(), PDO::PARAM_STR);
            $stmt->bindValue(11,$empresa->getFacebook(), PDO::PARAM_STR);
            $stmt->bindValue(12,$empresa->getInstagram(), PDO::PARAM_STR);
            $stmt->bindValue(13,$empresa->getTwitter(), PDO::PARAM_STR);
            $stmt->bindValue(14,$empresa->getUserId(), PDO::PARAM_INT);
            $stmt->execute();

            return array(
                'status'    => 200,
                'message'   => "SUCCESS"
            );

        } catch (PDOException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }

    }

    public function update(Empresa $empresa){

        $conn = \Database::conexao();

        $query_foto = ( !empty($empresa->getFotoMarca()) ) ? ',empresa_foto_marca = ?' : '';

        $sql = "UPDATE empresa
                SET  empresa_nome  = ?,
                     empresa_telefone = ?,
                     empresa_cnpj = ?,
                     empresa_cep = ?,
                     empresa_lat = ?,
                     empresa_long = ?,
                     empresa_numero_endereco = ?,
                     empresa_complemento_endereco = ?,
                     empresa_data_fundacao = ?,
                     empresa_facebook = ?,
                     empresa_instagram = ?,
                     empresa_twitter = ? 
                     ".$query_foto."
                WHERE empresa_id = ?";
        $stmt = $conn->prepare($sql);

        try {
            $stmt->bindValue(1,$empresa->getNome(), PDO::PARAM_STR);
            $stmt->bindValue(2,$empresa->getTelefone(), PDO::PARAM_STR);
            $stmt->bindValue(3,$empresa->getCnpj(), PDO::PARAM_STR);
            $stmt->bindValue(4,$empresa->getCep(), PDO::PARAM_STR);
            $stmt->bindValue(5,$empresa->getLatitude(), PDO::PARAM_STR);
            $stmt->bindValue(6,$empresa->getLongitude(), PDO::PARAM_STR);
            $stmt->bindValue(7,$empresa->getNumeroEndereco(), PDO::PARAM_INT);
            $stmt->bindValue(8,$empresa->getComplementoEndereco(), PDO::PARAM_STR);
            $stmt->bindValue(9,$empresa->getDataFundacao(), PDO::PARAM_STR);
            $stmt->bindValue(10,$empresa->getFacebook(), PDO::PARAM_STR);
            $stmt->bindValue(11,$empresa->getInstagram(), PDO::PARAM_STR);
            $stmt->bindValue(12,$empresa->getTwitter(), PDO::PARAM_STR);

            $aux = 13;
            if (!empty($query_foto)){
                $stmt->bindValue($aux,$empresa->getFotoMarca());
                $aux++;
            }

            $stmt->bindValue($aux,$empresa->getId(), PDO::PARAM_INT);
            $stmt->execute();

            return array(
                'status'    => 200,
                'message'   => "SUCCESS"
            );

        } catch (PDOException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }

    }

    public function enabled(Empresa $empresa){

        $conn = \Database::conexao();

        $sql = "UPDATE empresa
                SET  empresa_enabled  = ?
                WHERE empresa_id = ?";
        $stmt = $conn->prepare($sql);

        $enabled = ($empresa->getEnabled() == 'true')? true : false ;

        try {
            $stmt->bindValue(1,$enabled);
            $stmt->bindValue(2,$empresa->getId(), PDO::PARAM_INT);
            $stmt->execute();

            return array(
                'status'    => 200,
                'message'   => "SUCCESS"
            );

        } catch (PDOException $ex) {
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            );
        }

    }
}