import { useTranslate } from '@refinedev/core'
import { FormPage, FormPageItem } from '@duxweb/dux-refine'
import { Input, InputNumber, Textarea } from 'tdesign-react/esm'
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
    </FormPage>
  )
}

export default Index
