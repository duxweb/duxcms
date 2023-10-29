import { useTranslate, useCustom } from '@refinedev/core'
import { FormModal } from '@duxweb/dux-refine'
import { Form, Input, Tree } from 'tdesign-react/esm'

const Page = (props: Record<string, any>) => {
  const translate = useTranslate()

  const { data } = useCustom({
    method: 'get',
    url: 'system/role/permission',
  })

  const permissionData = data?.data as Array<any> | undefined

  return (
    <FormModal id={props?.id}>
      <Form.FormItem label={translate('system.role.fields.name')} name='name'>
        <Input />
      </Form.FormItem>
      <Form.FormItem label={translate('system.role.fields.permission')} name='permission'>
        <Tree
          keys={{
            value: 'name',
            label: 'label',
          }}
          checkable
          data={permissionData}
        />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
