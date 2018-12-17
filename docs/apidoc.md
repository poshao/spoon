# APIdoc使用说明
## 1.概述

<h3>根据注释信息生成RESTful的API文档</h3>

## 2.使用说明
### 2.1 配置文件[apidoc.json]
基本配置项
|名称|描述|
|-|-|
|name|Name of your project.If no apidoc.json with the field exists, then apiDoc try to determine the the value from package.json.|
|version|Version of your project.If no apidoc.json with the field exists, then apiDoc try to determine the the value from package.json.|
|description|Introduction of your project.If no apidoc.json with the field exists, then apiDoc try to determine the the value from package.json.|
|title|Browser title text.|
|url|Prefix for api path (endpoints), e.g. https://api.github.com/v1|
|sampleUrl|If set, a form to test an api method (send a request) will be visible. See @apiSampleRequest for more details.|
|<b>header</b>|
|&nbsp;&nbsp;&nbsp;&nbsp;title|Navigation text for the included header.md file.(watch Header / Footer)|
|&nbsp;&nbsp;&nbsp;&nbsp;filename|Filename (markdown-file) for the included header.md file.
|<b>footer</b>|
|&nbsp;&nbsp;&nbsp;&nbsp;title|Navigation text for the included footer.md file.|
|&nbsp;&nbsp;&nbsp;&nbsp;filename|Filename (markdown-file) for the included footer.md file.
|order|A list of api-names / group-names for ordering the output. Not defined names are automatically displayed last.|

模板配置说明
|Name|Type|Description|
|-|-|-|
|<b>template</b>|
|    forceLanguage|String|Disable browser language auto-detection and set a specific locale.Example: <code>de</code>, <code>en</code>.View available locales here.|
|    withCompare|Boolean|Enable comparison with older api versions. Default: true|
|    withGenerator|Boolean|Output the generator information at the footer. Default: true|
|    jQueryAjaxSetup|Object|Set default values for Ajax requests.|

示例
``` json
{
    "name":"Spoon",
    "version":"0.1.0",
    "description":"RESTful 基本框架",
    "title":"Spoon framework",
    "url":"https://localhost",
    "sampleUrl":"https://localhost",
    "template":{
        "forceLanguage":"zh_cn",
        "withCompare": true,
        "withGenerator": false
    }
}
```
### 2.2生成文档
``` bat
$ apidoc -i ./input -o ./output
```

### 2.3参数
@api