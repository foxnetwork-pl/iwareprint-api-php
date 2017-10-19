<?php

/*
 * This file is subject to the terms and conditions defined in
 * file 'LICENSE.txt', which is part of this source code package.
 */

use iwareprint\criteria\ProductCriteria;
use iwareprint\Iwareprint;
use iwareprint\representations\OrderStatus;
use iwareprint\Request;

include_once 'iwareprint/Iwareprint.php';
include_once 'iwareprint/Request.php';
include_once 'iwareprint/Response.php';
include_once 'iwareprint/Util.php';
include_once 'iwareprint/criteria/OrderCriteria.php';
include_once 'iwareprint/criteria/ProductCriteria.php';
include_once 'iwareprint/criteria/UserCriteria.php';
include_once 'iwareprint/forms/UserAddForm.php';
include_once 'iwareprint/IwareprintException.php';
include_once 'iwareprint/representations/Param.php';
include_once 'iwareprint/representations/OrderStatus.php';
include_once 'iwareprint/representations/OrderProduct.php';
include_once 'iwareprint/representations/Order.php';
include_once 'iwareprint/representations/OrderGroup.php';
include_once 'iwareprint/representations/ProductRepresentation.php';
include_once 'iwareprint/representations/ParameterRepresentation.php';
include_once 'iwareprint/representations/ParameterValueRepresentation.php';
include_once 'iwareprint/representations/OrderPackage.php';
include_once 'iwareprint/representations/ShipmentMethodRepresentation.php';
include_once 'iwareprint/representations/AddressData.php';
include_once 'iwareprint/representations/Url.php';
include_once 'iwareprint/representations/User.php';
include_once 'iwareprint/representations/OrderFileRepresentation.php';
include_once 'iwareprint/forms/OrderGroupAddForm.php';
include_once 'iwareprint/forms/CalculationForm.php';


$iwareprint = new Iwareprint("http://twojadrukarnia.iwareprint.pl/","-- API KEY --");

class Examples {

    private $api;

    function __construct(iwareprint\Iwareprint $api) {
        $this->api = $api;
    }

    /**
     * 
     * @return iwareprint\Iwareprint
     */
    private function getApi() {
        return $this->api;
    }

    /**
     * 
     * @return iwareprint\representations\ProductRepresentation
     */
    public function getFirstProduct() {
        $criteria = new \iwareprint\criteria\ProductCriteria();
        $criteria->setLimit(1);
        $criteria->setOrderBy("position");
        $criteria->setOrderByType("asc");
        return $this->getApi()->findProducts($criteria)[0];
    }

    public function getProductById() {
        return $this->getApi()->getProduct(1);
    }

    public function getLastOrder() {
        $criteria = new \iwareprint\criteria\OrderCriteria();
        $criteria->setLimit(1);
        $criteria->setOrderBy("id");
        $criteria->setOrderByType("desc");
        return $this->getApi()->findOrders($criteria);
    }

    public function addOrderGroup() {
        $product = $this->getFirstProduct();


        $form = new iwareprint\forms\OrderGroupAddForm;
        $form->setEmail("test@koszmail.pl");
        $form->setPhone("345345345");
        $form->setInvoiceTaxId("234234234");

        $addressData = new \iwareprint\representations\AddressData();
        $addressData->setApartmentNumber(33);
        $addressData->setBuildingNumber(44);
        $addressData->setCity("Wrocław");
        $addressData->setCompany("Testowe");
        $addressData->setFirstName("Jan");
        $addressData->setLastName("Kowalski");
        $addressData->setPostal("54-060");
        $addressData->setStreet("Testowa");
        $addressData->setPhone("234234234");

        $form->setInvoiceData($addressData);
        $form->setSenderData($addressData);
        $form->setShipmentData($addressData);

        $form->setShipmentMethodId(217);



        $order = new \iwareprint\representations\OrderProduct;

        $params = [];
        foreach ($product->getParameters() as $parameter) {
            $param = new iwareprint\representations\Param();
            $param->setParameterId($parameter->getId());
            $param->setValue($parameter->getValues()[0]->getId());
            $params[] = $param;
        }
        
        $calculationForm = new iwareprint\forms\CalculationForm();
        $calculationForm->setParams($params);
        $calculationForm->setProductId($product->getId());
        $calculation = $this->getApi()->calculate($calculationForm);

        $order->setPoints($calculation->getPoints());
        $order->setPrice($calculation->getPrice());
        $order->setProductId($product->getId());
        $order->setRealization($calculation->getRealization());
        $order->setWeight($calculation->getWeight());
        $order->setParams($params);


        $order1 = clone $order;
        $order1->setPrice($order->getPrice() * 2);
        $order2 = clone $order;
        $order2->setPrice($order->getPrice() * 3);
        $form->setOrders([$order, $order1, $order2]);
        $form->setUserId(1);
        return $this->getApi()->addOrderGroup($form);
    }

    public function uploadFileToOrder() {
        return $this->getApi()->uploadOrderFile(222, "Y:/htdocs/IwareprintApiClient/Koala.jpg");
    }

    public function downloadOrderFile() {
        $this->getApi()->downloadOrderFile(284, "test.jpg");
    }

    public function addUser() {
        $userAddForm = new iwareprint\forms\UserAddForm();
        $userAddForm->setApartmentNumber(10);
        $userAddForm->setBuildingNumber(9);
        $userAddForm->setCity("Wrocław");
        $userAddForm->setCompanyName("Testowa");
        $userAddForm->setEmail("test".time()."@koszmail.pl");
        $userAddForm->setTaxId(time());
        $userAddForm->setStreet("Testowa");
        $userAddForm->setName("Tomasz");
        $userAddForm->setSurname("Lis");
        $userAddForm->setPassword("haslozapi123");
        $userAddForm->setPhone("504938234");
        $userAddForm->setPostal("54-060");
        $userAddForm->setShipmentApartmentNumber(10);
        $userAddForm->setShipmentBuildingNumber(9);
        $userAddForm->setShipmentCity("Wrocław");
        $userAddForm->setShipmentCompanyName("Testowa");
        $userAddForm->setShipmentName("Tomasz");
        $userAddForm->setShipmentSurname("Lis");
        $userAddForm->setShipmentPhone("504938234");
        $userAddForm->setShipmentPostal("54-060");
        $userAddForm->setShipmentStreet("Testowa");
        $userAddForm->setType(iwareprint\forms\UserAddForm::USER_TYPE_COMPANY);

        return $this->getApi()->addUser($userAddForm);
    }

}
