# 用 Laravel Reverb 和 Vue SPA 开发的聊天室

![](./demo.gif)

## 介绍

这是一个使用 Laravel Reverb 和 Vue SPA 开发的聊天室，支持多房间、多用户、实时聊天等功能。

## 技术栈

> [!NOTE]
> 前后端公用一个仓库，通过 Laravel Sanctum 来处理 SPA 登陆认证。

后端是 Laravel，采用 Laravel Reverb 作为 WebSocket 服务器，负责从 Event 事件中推送消息到前端。

前端是 Vue SPA，使用了 Vue Router 作为路由管理，Pinia 共享全局状态，Tailwind CSS 添加样式。

## 本地开发

常规的 Laravel 项目开发流程，不再赘述。

### `.env` 设置

配置 `.env` 文件时，确保广播连接设置为 `reverb`，如下：

```dotenv
BROADCAST_CONNECTION=reverb
```

> [!WARNING]
> `BROADCAST_CONNECTION` 默认是 log 哦，这里要改成 reverb，别像我部署的时候忘了改，浪费了一个小时


添加 Reverb 的配置，新项目安装 Reverb 时会自动生成并写入 `.env`，但是部署或者 git clone 时，需要手动添加，如下：

```dotenv
REVERB_APP_ID=321494
REVERB_APP_KEY=v7pw8vesuuhaguqg3zop
REVERB_APP_SECRET=0ikht21jxdfge9dyg6h5
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

> [!TIP]
> - `REVERB_APP_ID` `REVERB_APP_KEY` `REVERB_APP_SECRET` 具体是什么不重要，随便填写即可；
> - `REVERB_HOST` `REVERB_PORT` `REVERB_SCHEME` 只对 Vite 有效，如果你不喜欢默认的配置，从命令行才能修改。


如果需要 @kimi 支持，还需要添加 Moonshot 的 API：

> [!TIP]
> Moonshot 免费额度内可能限制较大，比如 3 RPM（每分钟最多 3 次）等

```dotenv
MOONSHOT_API_KEY=sk-xxx
```

### 本地运行

Reverb 是通过异步事件才能触发，因此得把队列跑起来：

```shell
php artisan queue:listen
```

然后再启动 Reverb 服务：

```shell
php artisan reverb:work --debug
```
