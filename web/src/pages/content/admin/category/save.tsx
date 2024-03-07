import { useTranslate, useList } from '@refinedev/core'
import { FormModal, UploadImageManage, useSelect } from '@duxweb/dux-refine'
import { Form, Input, Cascader, Select } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  const { data, isLoading } = useList({
    resource: 'content.category',
  })
  const list = data?.data || []

  const { options, queryResult: magicResult } = useSelect({
    resource: 'tools.magic',
    meta: {
      params: {
        inline: 1,
      },
    },
    optionLabel: 'label',
    optionValue: 'id',
  })

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('content.category.fields.parent')} name='parent_id'>
        <Cascader
          checkStrictly
          loading={isLoading}
          options={list}
          keys={{
            label: 'name',
            value: 'id',
          }}
          clearable
        />
      </Form.FormItem>
      <Form.FormItem label={translate('content.category.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('content.category.fields.image')} name='image'>
        <UploadImageManage />
      </Form.FormItem>
      <Form.FormItem label={translate('content.category.fields.magic')} name='magic_id'>
        <Select loading={magicResult.isLoading} options={options} clearable />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
