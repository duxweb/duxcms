import { FormModal, useUpload } from '@duxweb/dux-refine'
import { useInvalidate } from '@refinedev/core'
import { Form, Upload } from 'tdesign-react/esm'

const Page = () => {
  const uploadParams = useUpload()

  const invalidate = useInvalidate()

  const onClear = () => {
    invalidate({
      resource: 'tools.backup',
      invalidates: ['all'],
    })
  }
  return (
    <FormModal resource='tools/backup/import' onSubmit={onClear}>
      <Form.FormItem name='file'>
        <Upload {...uploadParams} theme='file' draggable className='w-full' />
      </Form.FormItem>
    </FormModal>
  )
}

export default Page
