<?php

use extensions\ExtensionAdminMenuElement;
use extensions\ExtensionConfig;
use extensions\ExtensionCustomView;
use extensions\ExtensionProxySetting;

include_once 'iwareprint/extension/ExtensionAdminMenuElement.php';
include_once 'iwareprint/extension/ExtensionConfig.php';
include_once 'iwareprint/extension/ExtensionCustomView.php';
include_once 'iwareprint/extension/ExtensionProxySetting.php';

switch($_REQUEST["action"]) {

    case "cart":
        var_dump($_REQUEST["system"]["cart"]);
        break;
    case "frontendheader":

        if (isSet($_REQUEST["system"]["userLogged"])) {
            echo "<h1>zalogowany użytkownik ".$_REQUEST["system"]["userLogged"]["email"]." </h1>";
        } else {
            echo "<h1>brak zalogowanego użytkownika</h1>";
        }
//        var_dump($_REQUEST);
        break;
    case "dashboardview":
        var_dump($_REQUEST["view"]["product"]);
        break;
    case "form":
        if (isSet($_REQUEST["system"]["POST"])) {
            echo "<h1>Wpisane pole to -> ".$_REQUEST["system"]["POST"]["testField"]."</h1>";
            var_dump($_REQUEST);
        } else {
            echo "<h1>Przykładowy formularz</h1>";
            echo "<form action='' method='post'>Testowe pole<input name='testField' /><input type='submit' /></form>";
        }

        break;
    case "setsettings":
        $key = $_REQUEST["apiAccessKey"];
        $applicationConfirmKey = $_REQUEST["applicationConfirmKey"];
        $extensionPrivateKey = $_REQUEST["extensionPrivateKey"];
        file_put_contents("apiaccesskey","\n".$extensionPrivateKey."\n".$key."\n".$key,FILE_APPEND);
        break;
    case "config":
        $baseUrl = "http://".$_SERVER["SERVER_NAME"]."/IwareprintApiClient/";
        $config = new ExtensionConfig();
        $config->setSetSettingsUrl($baseUrl."extension.php?action=setsettings");
        /* "backend.dashboard.top"=>"#{Backend - dashboard góra}",
            "backend.dashboard.bottom"=>"#{Backend - dashboard dół}",
          "backend.head"=>"#{Backend - sekcja head}",
          "backend.header"=>"#{Backend - header}",
          "backend.footer"=>"#{Backend - footer}",
            "backend.order-group.right"=>"#{Backend - zamówienie prawa sekcja}",
            "backend.order-file-list-slot"=>"#{Backend - lista plików}",
            "technology-card"=>"#{Karta technologiczna}",
            "frontend.header"=>"#{Frontend - header}",
            "frontend.head"=>"#{Frontend - sekcja head}",
            "frontend.footer"=>"#{Frontend - footer}",
         */

        $customView = new ExtensionCustomView();
        $customView->setContent($baseUrl."extension.php?action=dashboardview");
        $customView->setPath("frontend.product");
        $customView->setContentType(ExtensionCustomView::TYPE_EXTERNAL);

        $customViewFrontend = new ExtensionCustomView();
        $customViewFrontend->setContent($baseUrl."extension.php?action=frontendheader");
        $customViewFrontend->setPath("frontend.user-panel");
        $customViewFrontend->setContentType(ExtensionCustomView::TYPE_EXTERNAL);

        $customViewCart = new ExtensionCustomView();
        $customViewCart->setContent($baseUrl."extension.php?action=cart");
        $customViewCart->setPath("frontend.cart");
        $customViewCart->setContentType(ExtensionCustomView::TYPE_EXTERNAL);

        $config->setCustomViews([$customView,$customViewFrontend,$customViewCart]);
        $adminMenuLink = new ExtensionAdminMenuElement();
        $adminMenuLink->setIcon("coffee"); //font-awesome
        $adminMenuLink->setSection("settings");
        $adminMenuLink->setTitle("Testowy formularz");
        $adminMenuLink->setUrl($baseUrl."extension.php?action=form");
        /*
         *  ("order", "#{Zamówienia}"),
            ("order-modify", "#{Zamówienia - modyfikacja}"),
            ("order-file", "#{Pliki do zleceń}"),
            ("order-file-modify", "#{Pliki do zleceń - modyfikacja}"),
            ("order-file-download", "#{Pliki do zleceń - pobieranie}"),
            ("user", "#{Klienci}"),
            ("user-modify", "#{Klienci - modyfikacja}"),
            ("product", "#{Produkty}"),
            ("product-modify", "#{Produkty - modyfikacja}"),
            ("shipment-methods", "#{Metody wysyłki}"),
         */
        $config->setAdminMenuElements([$adminMenuLink]);

        $proxy = new ExtensionProxySetting();
        $proxy->localUri = "test-extension"; // result path http://printinghouse.iwareprint.pl/px/test-extension/
        $proxy->setRemoteUrl($baseUrl."extension.php?action=testextensionexample");
        $config->setProxySettings([$proxy]);

        $config->setApiAccessSections(["order","order-modify","user","user-modify"]);
        file_put_contents("apiaccesskey",$_REQUEST["extensionPrivateKey"]); // klucz prywatny rozszerzenia
        echo json_encode($config);
        break;
    default:
        echo "<h1>action ".$_REQUEST["action"]."</h1>";
        var_dump($_REQUEST);
        var_dump($_SERVER);
}
