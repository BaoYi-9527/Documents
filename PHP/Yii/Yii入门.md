## Yii入门

### 1. 安装部署Yii

#### 1.1 Composer

`composer create-project --prefer-dist yiisoft/yii2-app-basic basic`

> 习惯了 Laravel 的 `dd()` 的，可以引入 `composer require symfony/var-dumper`

#### 1.2 验证

`php yii serve` 

```bash
D:php\YiiBasic>php yii serve
Server started on http://localhost:8080/
Document root is "D:\php\YiiBasic/web"
Quit the server with CTRL-C or COMMAND-C.
```

#### 1.3 检验当前PHP环境

1. 复制 `/requirements.php` 到 `/web/requirements.php`，然后通过浏览器访问 URL `http://localhost/requirements.php`
2. 项目根目录下执行 `php requirements.php`

#### 1.4 配置Web服务器

> 1. 项目入口文件为 `web/index.php` 文件 ;
> 2. 伪静态 `try_files $uri $uri/ /index.php$is_args$args;` ;
> 3. 使用 Nginx 配置时，应该在 `php.ini` 文件中设置 `cgi.fix_pathinfo=0` ， 能避免掉很多不必要的 `stat()` 系统调用。



**推荐使用的Apache配置**

```apacheconfig
# 设置文档根目录为 "basic/web"
DocumentRoot "path/to/basic/web"

<Directory "path/to/basic/web">
    # 开启 mod_rewrite 用于美化 URL 功能的支持（译注：对应 pretty URL 选项）
    RewriteEngine on
    # 如果请求的是真实存在的文件或目录，直接访问
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    # 如果请求的不是真实文件或目录，分发请求至 index.php
    RewriteRule . index.php

    # if $showScriptName is false in UrlManager, do not allow accessing URLs with script name
    RewriteRule ^index.php/ - [L,R=404]
    
    # ...其它设置...
</Directory>
```

**推荐的 Nginx 配置**

```nginx
server {
    charset utf-8;
    client_max_body_size 128M;

    listen 80; ## listen for ipv4
    #listen [::]:80 default_server ipv6only=on; ## listen for ipv6

    server_name mysite.test;
    root        /path/to/basic/web;
    index       index.php;

    access_log  /path/to/basic/log/access.log;
    error_log   /path/to/basic/log/error.log;

    location / {
        # Redirect everything that isn't a real file to index.php
        try_files $uri $uri/ /index.php$is_args$args;
    }

    # uncomment to avoid processing of calls to non-existing static files by Yii
    #location ~ \.(js|css|png|jpg|gif|swf|ico|pdf|mov|fla|zip|rar)$ {
    #    try_files $uri =404;
    #}
    #error_page 404 /404.html;

    # deny accessing php files for the /assets directory
    location ~ ^/assets/.*\.php$ {
        deny all;
    }
    
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass 127.0.0.1:9000;
        #fastcgi_pass unix:/var/run/php5-fpm.sock;
        try_files $uri =404;
    }

    location ~* /\. {
        deny all;
    }
}
```

### 2. 运行应用

#### 2.1 目录结构

```text
basic/                  应用根目录
    composer.json       Composer 配置文件, 描述包信息
    config/             包含应用配置及其它配置
        console.php     控制台应用配置信息
        web.php         Web 应用配置信息
    commands/           包含控制台命令类
    controllers/        包含控制器类
    models/             包含模型类
    runtime/            包含 Yii 在运行时生成的文件，例如日志和缓存文件
    vendor/             包含已经安装的 Composer 包，包括 Yii 框架自身
    views/              包含视图文件
    web/                Web 应用根目录，包含 Web 入口文件
        assets/         包含 Yii 发布的资源文件（javascript 和 css）
        index.php       应用入口文件
    yii                 Yii 控制台命令执行脚本
```



#### 2.2 请求生命周期

1. 用户向入口脚本 `web/index.php` 发起请求。
2. 入口脚本加载应用配置并创建一个应用实例去处理请求。
3. 应用通过请求组件解析请求的路由。
4. 应用创建一个控制器实例去处理请求。
5. 控制器创建一个动作实例并针对操作执行过滤器。
6. 如果任何一个过滤器返回失败，则动作取消。
7. 如果所有过滤器都通过，动作将被执行。
8. 动作会加载一个数据模型，或许是来自数据库。
9. 动作会渲染一个视图，把数据模型提供给它。
10. 渲染结果返回给响应组件。
11. 响应组件发送渲染结果给用户浏览器。



#### 2.3 路由管理

**Yii中的路由支持俩种格式：**

1. path 格式： `http://www.yii.local.com/site/say?message=Hello+World`
2. get 格式(默认)：`http://www.yii.local.com/?r=site/say&message=Hello+World`

**Yii 中的路由的主要作用：**

1. 根据请求URL找到应对的控制器和方法 `coontrollerID/ActionID`
2. 根据提供的参数及规则生成URL

**`config\web.php` 下可以对 URL 路由组件 `urlManager` 进行设置，相关参数：**

1. `rules(Array)` ：，指定 URL 双向解析规则
2. `urlSuffix(String)` ：指定 pathinfo 模式时的 URL 后缀
3. `showScriptName(Boolean)` ：是否在 URL 中显示脚本文件名称 `index.php`，为 `false` 时需要服务器支持
4. `apeendParams(Boolean)`：pathinfo 模式下是否将参数追加到URL上，此项参数一般用于生成URL场景。设置为`true`时，参数将以斜线分隔并追加到路径后面。设为`false`时将以`query string`方式追加
5. `routeVar(String)`：仅在`get`形式时有效，指定路由信息的变量名称，比如默认的 `r`
6. `caseSensitive(Boolean)`：路由信息是否区分大小写，如果设置为 `false` ，请求路由信息被转换为小写
7. `cacheID(String)`：路由缓存所使用的缓存组件名称
8. `useStrictParsing(Boolean)`：仅在 Path 形式时有效，设置是否使用严格的URL解析



```
# 示例(设置一个 pathInfo 格式的路由解析)
'urlManager'   => [
    'enablePrettyUrl'     => true,  # 是否开启URL美化
    'showScriptName'      => false, # 是否在构造的URL中显示脚本文件名称 index.php
    'rules'               => [
//        ['class' => 'yii\rest\UrlRule', 'controller' => 'site'],
    ],
],
```

#### 2.4 请求与响应

**请求**

```php
// use yii\web\Request;
public function actionRequest(Request $request)
{
    $method     = $request->method;
    $getParams  = $request->post();
    $postParams = $request->get();
    $bodyParams = $request->bodyParams;
    $param      = $request->getBodyParam('a');

    dump($request->isAjax); // 请求是一个 ajax 请求
    dump($request->isGet);  // 请求方法是 GET
    dump($request->isPost); // 请求方法是 POST
    dump($request->isPut);  // 请求方法是 PUT

    dump($request->url);            // /api/test/request?id=10086
    dump($request->absoluteUrl);    // http://www.yii.local.com/api/test/request?id=10086
    dump($request->hostInfo);       // http://www.yii.local.com
    dump($request->pathInfo);       // api/test/request
    dump($request->queryString);    // id=10086
    dump($request->baseUrl);        // "" [主机信息之后， 入口脚本之前的部分。]
    dump($request->scriptUrl);      // /index.php
    dump($request->serverName);     // www.yii.local.com
    dump($request->serverPort);     // 80


    $headers = $request->headers;       // 请求头
    dump($headers->get('Accept'));      // 获取请求头信息
    dump($headers->has('user-agent'));  // 判断请求头信息
    
    dump($request->userHost);   // 客户端主机名
    dump($request->userIP);     // 客户端IP地址

    dd($method, $getParams, $postParams, $bodyParams, $param);
}
```

**输入验证**

```php
<?php

namespace app\Forms\Test;

use yii\base\Model;

class TestForm extends Model
{
    public $username;
    public $password;
	
    // 验证规则
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
        ];
    }
}
```

*使用验证器*

```php
$model = new \app\models\ContactForm();

// 根据用户的输入填充到模型的属性中
$model->load(\Yii::$app->request->post());
// 等效于下面这样：
// $model->attributes = \Yii::$app->request->post('ContactForm');

if ($model->validate()) {
    // 所有输入通过验证
} else {
    // 验证失败: $errors 是一个包含错误信息的数组
    $errors = $model->errors;
}
```





**响应**

```php
public function actionResponse()
{
    $response = new Response();
    # 自定义状态码
    $response->statusCode = 403; // 默认 200

    # HTTP头部
    $headers = $response->headers;
    // 增加一个 Pragma 头，已存在的 Pragma 头不会被覆盖。
    $headers->add('Pragma', 'no-cache');
    // 设置一个Pragma 头. 任何已存在的 Pragma 头都会被丢弃
    $headers->set('Pragma', 'no-cache');
    // 删除 Pragma 头并返回删除的 Pragma 头的值到数组
    $values = $headers->remove('Pragma');

    # 响应主体
    $response->content = "hello world!!";

    # 响应格式化
    $response->format = Response::FORMAT_JSON;
    $response->data   = ['msg' => 'hello world!!!'];

    return $response;
}
```

Yii支持以下可直接使用的格式，每个实现了[formatter](https://www.yiichina.com/doc/api/2.0/yii-web-responseformatterinterface) 类， 可自定义这些格式器或通过配置 [yii\web\Response::$formatters](https://www.yiichina.com/doc/api/2.0/yii-web-response#$formatters-detail) 属性来增加格式器。

- [HTML](https://www.yiichina.com/doc/api/2.0/yii-web-response#FORMAT_HTML-detail)：通过 [yii\web\HtmlResponseFormatter](https://www.yiichina.com/doc/api/2.0/yii-web-htmlresponseformatter) 来实现。
- [XML](https://www.yiichina.com/doc/api/2.0/yii-web-response#FORMAT_XML-detail)：通过 [yii\web\XmlResponseFormatter](https://www.yiichina.com/doc/api/2.0/yii-web-xmlresponseformatter) 来实现。
- [JSON](https://www.yiichina.com/doc/api/2.0/yii-web-response#FORMAT_JSON-detail)：通过 [yii\web\JsonResponseFormatter](https://www.yiichina.com/doc/api/2.0/yii-web-jsonresponseformatter) 来实现。
- [JSONP](https://www.yiichina.com/doc/api/2.0/yii-web-response#FORMAT_JSONP-detail)：通过 [yii\web\JsonResponseFormatter](https://www.yiichina.com/doc/api/2.0/yii-web-jsonresponseformatter) 来实现。
- [RAW](https://www.yiichina.com/doc/api/2.0/yii-web-response#FORMAT_RAW-detail)：如果要直接发送响应而不应用任何格式，请使用此格式。



当[错误处理器](https://www.yiichina.com/doc/guide/2.0/runtime-handling-errors) 捕获到一个异常，会从异常中提取状态码并赋值到响应， 对于上述的 [yii\web\NotFoundHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-notfoundhttpexception) 对应 HTTP 404 状态码， 以下为 Yii 预定义的 HTTP 异常：

- [yii\web\BadRequestHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-badrequesthttpexception)：状态码 400。
- [yii\web\ConflictHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-conflicthttpexception)：状态码 409。
- [yii\web\ForbiddenHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-forbiddenhttpexception)：状态码 403。
- [yii\web\GoneHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-gonehttpexception)：状态码 410。
- [yii\web\MethodNotAllowedHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-methodnotallowedhttpexception)：状态码 405。
- [yii\web\NotAcceptableHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-notacceptablehttpexception)：状态码 406。
- [yii\web\NotFoundHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-notfoundhttpexception)：状态码 404。
- [yii\web\ServerErrorHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-servererrorhttpexception)：状态码 500。
- [yii\web\TooManyRequestsHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-toomanyrequestshttpexception)：状态码 429。
- [yii\web\UnauthorizedHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-unauthorizedhttpexception)：状态码 401。
- [yii\web\UnsupportedMediaTypeHttpException](https://www.yiichina.com/doc/api/2.0/yii-web-unsupportedmediatypehttpexception)：状态码 415。

如果想抛出的异常不在如上列表中，可创建一个 [yii\web\HttpException](https://www.yiichina.com/doc/api/2.0/yii-web-httpexception) 异常， 带上状态码抛出，如下：

```php
throw new \yii\web\HttpException(402);
```



#### 2.5 控制器



控制器由 *操作* 组成，它是执行终端用户请求的最基础的单元， 一个控制器可有一个或多个操作。



**终端用户通过所谓的路由寻找到动作，路由时包含一下部分的字符串：**

1. 模块ID: 仅存在于控制器属于非应用的模块;
2. 控制器ID: 同应用（或同模块如果为模块下的控制器） 下唯一标识控制器的字符串;
3. 操作ID: 同控制器下唯一标识操作的字符串。

路由使用格式：`ControllerID/ActionID`

如果属于模块下的控制器，使用如下格式：`ModuleID/ControllerID/ActionID`



**控制器ID**

通常情况下，控制器用来处理请求有关的资源类型， 因此控制器ID通常为和资源有关的名词。

1. 控制器ID应仅包含英文小写字母、数字、下划线、中横杠和正斜杠， 例如 `article` 和 `post-comment` 是真是的控制器ID， `article?`, `PostComment`, `admin\post`不是控制器ID。
2. 控制器Id可包含子目录前缀，例如 `admin/article` 代表 [controller namespace](https://www.yiichina.com/doc/api/2.0/yii-base-application#$controllerNamespace-detail) 控制器命名空间下 `admin`子目录中 `article` 控制器。 子目录前缀可为英文大小写字母、数字、下划线、正斜杠，其中正斜杠用来区分多级子目录(如 `panels/admin`)。

**控制器命名**

1. 将用正斜杠区分的每个单词第一个字母转为大写。注意如果控制器ID包含正斜杠， 只将最后的正斜杠后的部分第一个字母转为大写；
2. 去掉中横杠，将正斜杠替换为反斜杠;
3. 增加`Controller`后缀;
4. 在前面增加[controller namespace](https://www.yiichina.com/doc/api/2.0/yii-base-application#$controllerNamespace-detail)控制器命名空间。

示例：

1. `rticle` 对应 `app\controllers\ArticleController`;
2. `post-comment` 对应 `app\controllers\PostCommentController`;
3. `admin/post-comment` 对应 `app\controllers\admin\PostCommentController`;
4. `adminPanels/post-comment` 对应 `app\controllers\adminPanels\PostCommentController`.

**控制器部署**

可通过配置 [controller map](https://www.yiichina.com/doc/api/2.0/yii-base-module#$controllerMap-detail) 来强制上述的控制器ID和类名对应， 通常用在使用第三方不能掌控类名的控制器上。

```php
[
    'controllerMap' => [
        // 用类名申明 "account" 控制器
        'account' => 'app\controllers\UserController',

        // 用配置数组申明 "article" 控制器
        'article' => [
            'class' => 'app\controllers\PostController',
            'enableCsrfValidation' => false,
        ],
    ],
]
```

**创建动作**

创建操作可简单地在控制器类中定义所谓的 *操作方法* 来完成，操作方法必须是以`action`开头的公有方法。 操作方法的返回值会作为响应数据发送给终端用户。

```php
namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionHelloWorld()
    {
        return 'Hello World';
    }
}
```

**动作ID**

1. 操作通常是用来执行资源的特定操作，因此， 操作ID通常为动词，如`view`, `update`等。
2. 操作ID应仅包含英文小写字母、数字、下划线和中横杠，操作ID中的中横杠用来分隔单词。 例如`view`, `update2`, `comment-post`是真实的操作ID， `view?`, `Update`不是操作ID.
3. 可通过两种方式创建操作ID，内联操作和独立操作. An inline action is 内联操作在控制器类中定义为方法；独立操作是继承[yii\base\Action](https://www.yiichina.com/doc/api/2.0/yii-base-action)或它的子类的类。 内联操作容易创建，在无需重用的情况下优先使用； 独立操作相反，主要用于多个控制器重用， 或重构为[扩展](https://www.yiichina.com/doc/guide/2.0/structure-extensions)。

**内联动作**

内联动作指的是根据我们刚描述的操作方法。

动作方法的名字是根据操作ID遵循如下规则衍生：

1. 将每个单词的第一个字母转为大写;
2. 去掉中横杠;
3. 增加`action`前缀.

> 操作方法的名字*大小写敏感*，如果方法名称为`ActionIndex`不会认为是操作方法， 所以请求`index`操作会返回一个异常， 也要注意操作方法必须是公有的， 私有或者受保护的方法不能定义成内联操作。

**独立操作**

1. 如果你计划在不同地方重用相同的操作， 或者你想重新分配一个操作，需要考虑定义它为*独立操作*。
2. 独立操作通过继承 [yii\base\Action](https://www.yiichina.com/doc/api/2.0/yii-base-action) 或它的子类来定义。 例如Yii发布的 [yii\web\ViewAction](https://www.yiichina.com/doc/api/2.0/yii-web-viewaction)  和 [yii\web\ErrorAction](https://www.yiichina.com/doc/api/2.0/yii-web-erroraction) 都是独立操作。
3. 要使用独立操作，需要通过控制器中覆盖 [yii\base\Controller::actions()](https://www.yiichina.com/doc/api/2.0/yii-base-controller#actions()-detail) 方法在 *action map* 中申明， 如下例所示：

```php
public function actions()
{
    return [
        // 用类来申明"error" 动作
        'error' => 'yii\web\ErrorAction',

        // 用配置数组申明 "view" 动作
        'view' => [
            'class' => 'yii\web\ViewAction',
            'viewPrefix' => '',
        ],
    ];
}
```

*简单封装一个BaseAction*

```php
<?php

namespace app\Services\Base;

use yii\base\Action;
use yii\web\Response;

class BaseAction extends Action
{

    /**
     * Notes:成功返回
     * User: weicheng
     * DateTime: 2022/5/21 23:09
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return false|string
     */
    public function successResponse(array $data = [], $msg = 'success', $code = 200)
    {
        return $this->asJson([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
    }

    /**
     * Notes:失败返回
     * User: weicheng
     * DateTime: 2022/5/21 23:10
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return false|string
     */
    public function errorResponse($code = 403, $msg = 'error', $data = [])
    {
        return $this->asJson([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ]);
    }

    /**
     * Notes:json response
     * User: weicheng
     * DateTime: 2022/5/22 14:39
     * @param array $arr
     * @return false|string
     */
    private function asJson(array $arr)
    {
        $response         = new Response();
        $response->format = Response::FORMAT_JSON;
        $response->data   = $arr;
        return $response;
    }
}
```

*实现一个公用Action*

```php
<?php

namespace app\Services\Actions;

use app\Services\Base\BaseAction;

class TestAction extends BaseAction
{

    /**
     * Notes:测试
     * User: weicheng
     * DateTime: 2022/5/22 14:34
     */
    public function run()
    {
        return $this->successResponse([], "Hello World!!");
    }
}
```

*控制器中引用这个Action*

```php
<?php

namespace app\controllers\api;

use app\Services\Actions\TestAction;
use app\Services\Base\BaseController;

class TestController extends BaseController
{
    public function actions()
    {
        return [
            'action-test' => TestAction::class
        ];
    }

    public function actionTest()
    {
        $this->successResponse();
    }


}
```







