import { useCallback, useState } from 'react'
import { useTranslate } from '@refinedev/core'
import { Button, MessagePlugin, Loading, Switch } from 'tdesign-react/esm'
import { Modal, useClient, useModal } from '@duxweb/dux-refine'
import dayjs from 'dayjs'

interface PageProps {
  data: Record<string, any>
}

const Page = ({ data }: PageProps) => {
  const translate = useTranslate()
  const [build, setBuild] = useState(false)
  const client = useClient()
  const [loading, setLoading] = useState(false)
  const [log, setLog] = useState('')

  const { onClose } = useModal()

  const submit = useCallback(() => {
    setLoading(true)
    client
      .request('cloud/apps/update', 'post', {
        data: {
          name: data.name,
          build: build,
        },
      })
      .then((res) => {
        if (res.code !== 200) {
          MessagePlugin.error(res.message)
          return
        }
        setLog(res?.data?.content)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  return (
    <>
      {log ? (
        <div className='p-4'>
          <pre className='overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
        </div>
      ) : (
        <>
          <div className='p-4'>
            <div className='mb-2'>{translate('cloud.apps.tips.update')}</div>
            <div className='mb-2'>
              {translate('cloud.apps.fields.verCur')}
              {dayjs(data.local_time * 1000).format('YYYY-MM-DD HH:mm:ss')}
            </div>
            <div>
              {translate('cloud.apps.fields.target')}
              <span className='text-error'>
                {dayjs(data.time * 1000).format('YYYY-MM-DD HH:mm:ss')}
              </span>
            </div>
          </div>
          <div className='p-4'>
            <div className='mb-2'>{translate('cloud.apps.validator.build')}</div>
            <Switch value={build} onChange={(v) => setBuild(v)} />
            <div className='mt-2 text-placeholder'>{translate('cloud.apps.help.build')}</div>
          </div>
          <Modal.Footer>
            <Button variant='outline' onClick={onClose}>
              {translate('cloud.apps.action.close')}
            </Button>
            <Button
              onClick={() => {
                submit()
              }}
            >
              {translate('cloud.apps.action.update')}
            </Button>
          </Modal.Footer>
        </>
      )}

      <Loading
        loading={loading}
        fullscreen
        preventScrollThrough={true}
        text={translate('cloud.apps.tips.loading')}
      ></Loading>
    </>
  )
}

export default Page
