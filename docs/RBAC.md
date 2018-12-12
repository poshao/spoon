# 基于角色的用户管理设计

## 1. 数据表

## 2. API设计

### 用户 (User)
user
userinfo
token
group
role
permission
link


1. 注册 (register) [post] auth/user workid,password
2. 登录 (login) [post] auth/token workid,password
3. 注销 (logout) [post]
4. 验证 (varify)
5. 个人信息 (info)

### 分组 (Group)
