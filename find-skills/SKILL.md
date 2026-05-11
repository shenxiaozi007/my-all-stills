---
name: find-skills
description: 帮助用户发现和安装 agent skills。当用户询问“怎么做某件事”“找一个能做 X 的 skill”“有没有某种 skill”，或表达想扩展能力时使用；适用于用户需要查找可能已经存在的可安装 skill 的场景。
---

# Find Skills

这个 Skill 用来帮助用户从开放的 agent skills 生态中发现、评估并安装合适的 skills。

## 什么时候使用

当用户出现以下需求时，使用这个 Skill：

- 询问“怎么做 X”，并且 X 可能是一个已有 skill 能覆盖的常见任务。
- 说“帮我找一个做 X 的 skill”或“有没有能做 X 的 skill”。
- 问“你能不能做 X”，而 X 属于比较专门的能力。
- 表达想扩展 agent 能力。
- 想搜索工具、模板或工作流。
- 提到希望在某个领域获得帮助，例如设计、测试、部署等。

## Skills CLI 是什么

Skills CLI（`npx skills`）是开放 agent skills 生态的包管理工具。Skills 是模块化能力包，可以通过专门知识、工作流程和工具来扩展 agent 能力。

**常用命令：**

- `npx skills find [query]`：按关键词搜索 skills，或进入交互式搜索。
- `npx skills add <package>`：从 GitHub 或其他来源安装 skill。
- `npx skills check`：检查已安装 skills 是否有更新。
- `npx skills update`：更新全部已安装 skills。

**浏览 skills：** https://skills.sh/

## 如何帮助用户找 Skill

### 第一步：理解用户需要什么

当用户提出需求时，先判断：

1. 所属领域，例如 React、测试、设计、部署。
2. 具体任务，例如写测试、做动画、审查 PR。
3. 这个任务是否足够常见，是否可能已经有对应 skill。

### 第二步：先看排行榜

运行 CLI 搜索前，先查看 [skills.sh 排行榜](https://skills.sh/)，看看该领域是否已经有知名 skill。排行榜按安装量排序，通常能看到更常用、经过更多人验证的选项。

例如，Web 开发常见来源包括：

- `vercel-labs/agent-skills`：React、Next.js、Web 设计等方向。
- `anthropics/skills`：前端设计、文档处理等方向。

### 第三步：搜索 Skills

如果排行榜没有覆盖用户需求，运行搜索命令：

```bash
npx skills find [query]
```

示例：

- 用户问“怎么让 React 应用更快？” → `npx skills find react performance`
- 用户问“能不能帮我做 PR review？” → `npx skills find pr review`
- 用户说“我需要生成 changelog” → `npx skills find changelog`

### 第四步：推荐前先判断质量

**不要只因为搜索结果里出现了某个 skill 就直接推荐。** 推荐前要检查：

1. **安装量**：优先考虑 1000+ 安装量的 skill。低于 100 的要谨慎。
2. **来源可信度**：官方或知名来源，例如 `vercel-labs`、`anthropics`、`microsoft`，通常比陌生作者更可信。
3. **GitHub stars**：检查来源仓库。低于 100 stars 的仓库要谨慎评估。

### 第五步：把可选方案告诉用户

找到相关 skills 后，向用户说明：

1. Skill 名称，以及它能做什么。
2. 安装量和来源。
3. 可执行的安装命令。
4. skills.sh 上的详情链接。

回复示例：

```
我找到一个可能有帮助的 skill：react-best-practices。
它提供 React 和 Next.js 性能优化相关指南，来源是 Vercel Engineering。
安装量：185K

安装命令：
npx skills add vercel-labs/agent-skills@react-best-practices

详情链接：https://skills.sh/vercel-labs/agent-skills/react-best-practices
```

### 第六步：询问是否安装

如果用户想继续，可以帮用户安装：

```bash
npx skills add <owner/repo@skill> -g -y
```

其中 `-g` 表示全局安装到用户级别，`-y` 表示跳过确认提示。

## 常见 Skill 分类

搜索时可以参考这些常见分类：

| 分类 | 示例关键词 |
| --- | --- |
| Web 开发 | react, nextjs, typescript, css, tailwind |
| 测试 | testing, jest, playwright, e2e |
| DevOps | deploy, docker, kubernetes, ci-cd |
| 文档 | docs, readme, changelog, api-docs |
| 代码质量 | review, lint, refactor, best-practices |
| 设计 | ui, ux, design-system, accessibility |
| 效率 | workflow, automation, git |

## 搜索技巧

1. **使用更具体的关键词**：`react testing` 通常比只搜 `testing` 更准确。
2. **尝试同义词**：如果 `deploy` 搜不到，可以试试 `deployment` 或 `ci-cd`。
3. **关注热门来源**：很多 skills 来自 `vercel-labs/agent-skills` 或 `ComposioHQ/awesome-claude-skills`。

## 找不到合适 Skill 时

如果没有找到相关 skill：

1. 明确告诉用户没有找到合适的现成 skill。
2. 说明仍然可以直接用通用能力帮助完成任务。
3. 如果这是高频需求，可以建议用户用 `npx skills init` 创建自己的 skill。

回复示例：

```
我搜索了和 “xyz” 相关的 skills，但没有找到合适结果。
我仍然可以直接帮你处理这个任务。

如果这是你经常会做的事情，也可以创建一个自己的 skill：
npx skills init my-xyz-skill
```
