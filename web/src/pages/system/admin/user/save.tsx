import { useTranslate } from '@refinedev/core'
import {
  FormModal,
  useUpload,
  useSelect,
  formatUploadSingle,
  getUploadSingle,
} from '@duxweb/dux-refine'
import { Form, Input, Upload, Select, Switch } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()
  const uploadParams = useUpload()

  const { options, onSearch } = useSelect({
    resource: 'system.role',
    optionLabel: 'name',
    optionValue: 'id',
  })

  return (
    <FormModal
      id={props?.id}
      initFormat={(data) => {
        data.avatar = formatUploadSingle(data?.avatar)
        return data
      }}
      saveFormat={(data) => {
        data.avatar = getUploadSingle(data?.avatar)
        return data
      }}
    >
      <Form.FormItem label={translate('system.user.fields.roles')} name='roles'>
        <Select filterable onSearch={onSearch} options={options} multiple />
      </Form.FormItem>
      <Form.FormItem label={translate('system.user.fields.username')} name='username'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('system.user.fields.nickname')} name='nickname'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('system.user.fields.avatar')} name='avatar'>
        <Upload {...uploadParams} theme='image' accept='image/*' />
      </Form.FormItem>
      <Form.FormItem label={translate('system.user.fields.password')} name='password'>
        <Input type='password' autocomplete='new-password' />
      </Form.FormItem>
      <Form.FormItem label={translate('system.user.fields.status')} name='status'>
        <Switch />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
