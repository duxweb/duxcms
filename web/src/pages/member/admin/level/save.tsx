import { useTranslate } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input, InputNumber, Radio } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('member.level.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('member.level.fields.growth')} name='growth'>
        <InputNumber />
      </Form.FormItem>
      <Form.FormItem label={translate('member.level.fields.type')} name='type' initialData={0}>
        <Radio.Group>
          <Radio value={0}>{translate('member.level.fields.common')}</Radio>
          <Radio value={1}>{translate('member.level.fields.recruit')}</Radio>
        </Radio.Group>
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
