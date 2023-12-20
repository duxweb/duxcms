import { useTranslate } from '@refinedev/core'
import {
  FormModal,
  useUpload,
  useSelect,
  formatUploadSingle,
  getUploadSingle,
} from '@duxweb/dux-refine'
import { Form, Input, Upload, Select, Switch, Radio, DatePicker } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()
  const uploadParams = useUpload()

  const { options, onSearch } = useSelect({
    resource: 'member.level',
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
      <Form.FormItem label={translate('member.user.fields.level')} name='level_id'>
        <Select filterable onSearch={onSearch} options={options} />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.nickname')} name='nickname'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.tel')} name='tel'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.email')} name='email'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.avatar')} name='avatar'>
        <Upload {...uploadParams} theme='image' accept='image/*' />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.sex')} name='sex' initialData={0}>
        <Radio.Group>
          <Radio value={0}>{translate('member.user.fields.privacy')}</Radio>
          <Radio value={1}>{translate('member.user.fields.man')}</Radio>
          <Radio value={2}>{translate('member.user.fields.woman')}</Radio>
        </Radio.Group>
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.birthday')} name='birthday'>
        <DatePicker />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.password')} name='password'>
        <Input type='password' autocomplete='new-password' />
      </Form.FormItem>
      <Form.FormItem label={translate('member.user.fields.status')} name='status'>
        <Switch />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
