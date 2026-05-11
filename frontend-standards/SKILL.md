---
name: frontend-standards
description: 当用户要求“按前端规范开发”“规划前端目录”“选择 Vue/小程序/uni-app/Taro”“制定多项目通用前端标准”，或需要前端工程目录、语言、框架、接口协作、用户端与管理后台边界规范时使用本 skill。用户端多端项目后续默认采用 uni-app + Vue 3 + TypeScript。
---

# 通用前端开发基线

## 适用范围
- 适用于多个业务项目的用户端、管理后台、H5、小程序和跨端前端工程。
- 与后端 skill 分工：本 skill 负责前端目录、语言、框架、状态、接口协作和构建规范；Laravel、Node、Python 等后端分层规范由对应后端 skill 承接。
- 开始前先识别产品端形态：用户端、管理后台、H5 活动页、小程序、App，或多端复用。

## 技术选型
- 用户端新项目默认使用 `uni-app + Vue 3 + TypeScript`，优先以一套代码覆盖 H5、微信小程序和 App。
- 即使当前只先上线 H5 或微信小程序，只要后续存在多端可能，也优先按 uni-app 工程启动，避免后续重写。
- 管理后台默认使用 `Vue 3 + TypeScript + Vite`，配套成熟后台组件库；除非项目已有 React 技术栈。
- 微信小程序原生仅作为例外：只有项目明确长期只做微信生态，且强依赖 uni-app 难以稳定覆盖的平台能力时才使用。
- `Taro + React` 仅在团队已有 React/Taro 资产，或明确需要 React 生态跨端时使用。
- 轻量官网、内容站、营销页若不需要小程序/App，可使用 `Vue 3 + TypeScript + Vite`；复杂内容站或 SSR/SEO 明确时再评估 Nuxt。

## 语言规定
- 新前端项目默认使用 TypeScript；不要新建纯 JavaScript 项目，除非目标平台或遗留项目限制明确。
- Vue 项目使用 `<script setup lang="ts">` 和 Composition API。
- 组件、工具、接口类型要显式建模；接口响应、分页、枚举、表单值、路由参数必须有类型。
- 禁止用 `any` 逃避建模；必要时用 `unknown` 加解析或收窄。
- 常量、枚举、字典项集中管理，避免页面里散落魔法字符串。
- 用户可见文案尽量集中到业务模块或 i18n/constant 层，避免在多个组件重复硬编码。

## 目录规定
uni-app 用户端工程优先使用以下结构：

```text
src/
  api/          # 请求封装和接口模块
  components/   # 跨模块通用组件
  constants/    # 枚举、字典、业务常量
  pages/        # 页面入口，对应 pages.json
  stores/       # Pinia 或轻量全局状态
  static/       # uni-app 静态资源
  styles/       # 全局样式、变量、主题
  types/        # 全局类型
  utils/        # 纯工具函数
  App.vue
  main.ts
  manifest.json
  pages.json
```

Web 管理后台或非跨端 H5 工程使用以下顶层目录，按项目类型删减：

```text
src/
  api/          # 请求封装和接口模块
  assets/       # 静态资源
  components/   # 跨模块通用组件
  composables/  # Vue 组合式逻辑
  constants/    # 枚举、字典、业务常量
  layouts/      # 布局框架
  pages/        # 页面级入口，小程序可对应 pages
  router/       # Web 路由
  stores/       # Pinia 或平台状态管理
  styles/       # 全局样式、变量、主题
  types/        # 全局类型
  utils/        # 纯工具函数
```

业务较复杂时使用按模块聚合：

```text
src/modules/{domain}/
  api.ts
  constants.ts
  types.ts
  components/
  pages/
```

## 项目放置
- 前端工程和后端工程默认独立目录，避免把页面、组件、构建产物混入后端 Controller / Business / Dao / Model 等分层。
- 单仓多项目可使用：
  - `apps/admin`：管理后台
  - `apps/client`：uni-app 用户端（H5 / 小程序 / App）
  - `apps/web`：仅 H5 / 官网 / 活动页
  - `apps/mp-wechat`：仅保留历史原生小程序或明确单端小程序项目
  - `packages/ui`：共享组件
  - `packages/shared`：共享类型、工具、常量
- 独立仓或简单项目可使用 `frontend-client`、`frontend-admin`、`frontend-web` 这类清晰目录名。
- 共享包只能放平台无关代码；不要把 Web DOM、小程序 API、后台组件库能力塞进通用 shared。

## 接口协作
- `api/` 只封装请求和数据转换，不写页面交互逻辑。
- 统一请求客户端处理 baseURL、token、错误码、超时、刷新登录、取消请求。
- API 类型优先来自 OpenAPI/Apifox/后端契约生成；没有生成链路时手写类型并跟接口文档同名同步。
- 分页、列表筛选、详情、创建、编辑、删除使用稳定命名，如 `getXxxList`、`getXxxDetail`、`createXxx`、`updateXxx`、`deleteXxx`。
- 文件上传、支付、登录、权限、审计等跨端差异能力必须在端侧适配层隔离。
- uni-app 中所有平台差异通过 adapter/composable 封装；页面里不要散落 `#ifdef`，除非是很小的 UI 条件。

## 状态与权限
- 页面局部状态放组件内；跨页面业务状态放 store；不要把一次性表单临时状态塞进全局 store。
- 管理后台权限以路由、菜单、按钮/操作点三层处理；后端仍必须做权限校验。
- 小程序权限、授权、上传、录音、定位、相册等能力要有拒绝、失败、重试和降级文案。

## 样式与组件
- 优先复用项目已有组件库和设计令牌；不要为单个页面创造一套局部视觉体系。
- 通用组件只承载稳定抽象；业务强耦合组件留在模块内。
- 表格、筛选、详情、弹窗、上传、步骤流等后台高频结构要形成可复用模式。
- 移动端和小程序页面优先保证触控区域、弱网反馈、空状态、错误状态、加载状态。

## 开发流程
1. 先确认端形态和目标用户：管理后台、用户端、小程序、H5、App 或跨端。
2. 再确认技术栈和工程放置方式，优先跟随已有项目。
3. 规划目录、路由、接口模块、类型和状态边界。
4. 实现页面和组件，补齐加载、空、错误、禁用、权限不足等状态。
5. 运行格式化、类型检查、构建或对应平台预览；交付时说明验证结果。

## 参考清单
- `references/frontend-checklist.md`
