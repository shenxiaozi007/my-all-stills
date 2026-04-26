---
name: laravel-git-flow
description: This skill should be used when the user asks to "按项目规范起分支", "确认 beta/master 发布流", "修复分支怎么命名", or needs repository-specific branch/release guidance.
---

# 本仓库 Git Flow 规范

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
