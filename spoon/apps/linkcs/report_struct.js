export default {
  report: {
    name: 'report-001',
    creator: '8123456',
    description: '测试报表',
    columns: [{
      key: 'dnei',
      title: '单号',
      sql: 'json_unquote(json_detail->$.dnei)',
      decode(value) {
        return value
      }
    }, {
      key: 'level',
      title: '紧急程度',
      sql: 'json_unquote(json_detail->\'$.level\')'
    }, {
      key: 'status',
      title: '状态',
      sql: 'status'
    }, {
      key: 'creator',
      title: '创建人',
      sql: 'creator'
    }]
  }
}