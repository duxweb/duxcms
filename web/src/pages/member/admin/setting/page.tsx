import { useTranslate } from '@refinedev/core'
import { Editor, FormPage, FormPageItem, useSelect } from '@duxweb/dux-refine'
import { Input, Radio, Select, Tabs } from 'tdesign-react/esm'
const Index = () => {
  const translate = useTranslate()

  const { options, onSearch, queryResult } = useSelect({
    resource: 'member.level',
    optionLabel: 'name',
    optionValue: 'id',
  })

  const {
    options: smsOptions,
    onSearch: smsOnSrarch,
    queryResult: smsQueryResult,
  } = useSelect({
    resource: 'sms.tpl',
    optionLabel: 'name',
    optionValue: 'id',
  })

  return (
    <FormPage
      rest
      action='edit'
      id={0}
      useFormProps={{
        meta: {
          mode: 'page',
        },
      }}
    >
      <Tabs defaultValue={0}>
        <Tabs.TabPanel
          value={0}
          label={translate('member.setting.tabs.base')}
          destroyOnHide={false}
        >
          <FormPageItem
            label={translate('member.setting.fields.register')}
            name='user_register'
            help={translate('member.setting.help.register')}
            initialData={1}
          >
            <Radio.Group>
              <Radio value={1}>开启</Radio>
              <Radio value={0}>关闭</Radio>
            </Radio.Group>
          </FormPageItem>

          <FormPageItem
            label={translate('member.setting.fields.level')}
            name='user_level'
            help={translate('member.setting.help.level')}
          >
            <Select options={options} onSearch={onSearch} loading={queryResult.isLoading}></Select>
          </FormPageItem>

          <FormPageItem
            label={translate('member.setting.fields.code')}
            name='user_code'
            help={translate('member.setting.help.code')}
          >
            <Select
              options={smsOptions}
              onSearch={smsOnSrarch}
              loading={smsQueryResult.isLoading}
            ></Select>
          </FormPageItem>

          <FormPageItem
            label={translate('member.setting.fields.tel')}
            name='user_service_tel'
            help={translate('member.setting.help.tel')}
          >
            <Input />
          </FormPageItem>
        </Tabs.TabPanel>
        <Tabs.TabPanel
          value={1}
          label={translate('member.setting.tabs.text')}
          destroyOnHide={false}
        >
          <FormPageItem
            label={translate('member.setting.fields.about')}
            name='user_about'
            help={translate('member.setting.help.about')}
          >
            <Editor />
          </FormPageItem>

          <FormPageItem
            label={translate('member.setting.fields.agreement')}
            name='user_agreement'
            help={translate('member.setting.help.agreement')}
          >
            <Editor />
          </FormPageItem>

          <FormPageItem
            label={translate('member.setting.fields.privacy')}
            name='user_privacy'
            help={translate('member.setting.help.privacy')}
          >
            <Editor />
          </FormPageItem>
        </Tabs.TabPanel>
      </Tabs>
    </FormPage>
  )
}

export default Index
