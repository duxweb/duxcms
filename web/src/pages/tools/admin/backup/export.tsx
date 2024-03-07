import { FormModal, useSelect } from '@duxweb/dux-refine'
import { useTranslate, useInvalidate } from '@refinedev/core'
import { Form, Transfer, Input } from 'tdesign-react/esm'

const Page = () => {
  const { options } = useSelect({
    resource: 'tools/backup/export',
    optionLabel: 'label',
    optionValue: 'value',
  })
  const translate = useTranslate()

  const invalidate = useInvalidate()

  const onClear = () => {
    invalidate({
      resource: 'tools.backup',
      invalidates: ['all'],
    })
  }
  return (
    <FormModal resource='tools/backup/export' onSubmit={onClear}>
      <Form.FormItem name='name' label={translate('tools.backup.fields.name')}>
        <Input />
      </Form.FormItem>
      <Form.FormItem name='data' label={translate('tools.backup.fields.data')}>
        <Transfer data={options} direction='both' showCheckAll targetSort='original' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
