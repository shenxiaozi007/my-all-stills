# Git Flow Checklist（本仓库）

## 开始前
- [ ] 已确认需求类型（新功能 / 修复）
- [ ] 已确认目标环境（tests / production）
- [ ] 已确认从 `master` 切分支

## 分支命名
- [ ] 分支前缀正确（`dev/` 或 `fix/`）
- [ ] 日期格式正确（`YYYYMMDD`）
- [ ] 需求标识为小写英文短语（`-` 连接）

## 协作约束
- [ ] 前后端分支名一致
- [ ] 同步了最新 `master`
- [ ] 未发生 `beta` 反向合并到 `dev/*` / `fix/*` / `master`

## 发布前
- [ ] tests 发布分支为 `beta`
- [ ] production 发布分支为 `master`
- [ ] 如涉及 migration，已核对执行环境与分支一致
