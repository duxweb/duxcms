import { useTranslate } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Input, InputNumber, Tabs, Select } from 'tdesign-react/esm'
const Index = () => {
  const translate = useTranslate()

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
      <Tabs placement={'top'} size={'medium'} defaultValue={0}>
        <Tabs.TabPanel label={translate('sms.setting.fields.sms')} value={0}>
          <FormPageItem
            label={translate('sms.setting.fields.interval')}
            name='sms_interval'
            help={translate('sms.setting.help.interval')}
          >
            <InputNumber theme='column' />
          </FormPageItem>

          <FormPageItem
            label={translate('sms.setting.fields.time')}
            name='sms_time'
            help={translate('sms.setting.help.time')}
          >
            <InputNumber theme='column' />
          </FormPageItem>

          <FormPageItem
            label={translate('sms.setting.fields.num')}
            name='sms_num'
            help={translate('sms.setting.help.num')}
          >
            <InputNumber theme='column' />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.expire')}
            name='sms_expire'
            help={translate('sms.setting.help.expire')}
          >
            <InputNumber theme='column' />
          </FormPageItem>
        </Tabs.TabPanel>
        <Tabs.TabPanel label={translate('sms.setting.fields.email')} value={1}>
          <FormPageItem
            label={translate('sms.setting.fields.emailHost')}
            name='email_host'
            help={translate('sms.setting.help.emailHost')}
          >
            <Input />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailPort')}
            name='email_port'
            help={translate('sms.setting.help.emailPort')}
          >
            <Input />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailName')}
            name='email_name'
            help={translate('sms.setting.help.emailName')}
          >
            <Input />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailUsername')}
            name='email_username'
            help={translate('sms.setting.help.emailUsername')}
          >
            <Input />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailPassword')}
            name='email_password'
            help={translate('sms.setting.help.emailPassword')}
          >
            <Input />
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailSecure')}
            name='email_secure'
            help={translate('sms.setting.help.emailSecure')}
            initialData={''}
          >
            <Select>
              <Select.Option value=''>none</Select.Option>
              <Select.Option value='tls'>tls</Select.Option>
              <Select.Option value='ssl'>ssl</Select.Option>
            </Select>
          </FormPageItem>
          <FormPageItem
            label={translate('sms.setting.fields.emailTimeout')}
            name='email_timeout'
            help={translate('sms.setting.help.emailTimeout')}
          >
            <InputNumber theme='column' />
          </FormPageItem>
        </Tabs.TabPanel>
      </Tabs>
    </FormPage>
  )
}

export default Index
