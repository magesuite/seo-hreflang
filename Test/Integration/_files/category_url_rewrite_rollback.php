<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\UrlRewrite\Model\UrlRewrite $urlRewrite */
$urlRewrite = $objectManager->create(\Magento\UrlRewrite\Model\UrlRewrite::class);
$urlRewrite->load('rewrite_for_active_category.html', 'request_path');
$urlRewrite->delete();
$urlRewrite->load('active_category.html', 'request_path');
$urlRewrite->delete();
