---
name: laravel-git-flow
description: This skill should be used when the user asks to "按项目规范起分支", "确认 beta/master 发布流", "修复分支怎么命名", or needs repository-specific branch/release guidance.
---

# 本仓库 Git Flow 规范

## 默认流程
1. 先确认当前分支、工作区状态、目标环境和需求类型（新功能/修复/发布）。
2. 根据需求类型选择 `dev/*` 或 `fix/*`，日期使用 `YYYYMMDD`。
3. 执行前说明将要进行的 git 操作；涉及合并、推送、覆盖工作区时先确认。
4. 如涉及 migration，提醒核对目标环境和分支对应关系。
5. 交付时说明当前分支、未提交改动和建议下一步。

## 需要先确认的情况
- 用户未说明需求类型，且无法从上下文判断是新功能还是修复。
- 工作区存在未提交改动，且即将切分支、合并、rebase、reset 或覆盖文件。
- 目标环境、发布日期或前后端分支命名需要协同。

## 注意事项 / 禁止项
- 不把 `beta` 合并回 `dev/*`、`fix/*` 或 `master`。
- 不在未确认时执行 `git reset`、覆盖 checkout、强推等高风险操作。
- 不默认创建中文、空格或大写分支名。

## 完成检查
- 分支前缀、日期、需求标识符合规范。
- 当前分支与目标环境对应关系已说明。
- 未提交改动已提醒，未误改无关文件。

## 标准分支
- `master`：生产分支
- `beta`：测试分支
- `dev/{日期}/{需求}`：需求开发分支（全小写）
- `fix/{日期}/{需求}`：修复分支（全小写）

## 命名建议
- 日期建议 `YYYYMMDD`（如 `dev/20260425/patient-import-optimize`）
- 需求标识用英文短语，使用 `-` 连接
- 避免中文、空格、大写

## 开发流转建议
1. 新需求从 `master` 切 `dev/*`。
2. 修复问题从 `master` 切 `fix/*`。
3. `master` 有更新时，及时合入当前开发分支。
4. 前后端分支名保持一致，便于联调。

## 重要约束
- 不要把 `beta` 合并回 `dev/*`、`fix/*` 或 `master`。
- 发布前检查当前环境与分支一致：
  - tests -> `beta`
  - production -> `master`

## 参考模板
- `references/git-flow-checklist.md`
- `references/git-flow-naming-examples.md`
