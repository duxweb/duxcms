import { useCallback, useState } from 'react'
import { useTranslate } from '@refinedev/core'
import { Input, Button, MessagePlugin, Loading, Switch } from 'tdesign-react/esm'
import { Modal, useClient } from '@duxweb/dux-refine'

const Page = () => {
  const translate = useTranslate()
  const [url, setUrl] = useState('')
  const [build, setBuild] = useState(false)
  const client = useClient()
  const [loading, setLoading] = useState(false)
  const [log, setLog] = useState('')

  const submit = useCallback((url: string) => {
    setLoading(true)
    client
      .request('cloud/apps/install', 'post', {
        data: {
          url: url,
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
      <div className='p-4'>
        <div className='mb-2'>{translate('cloud.apps.validator.url')}</div>
        <Input
          value={url}
          placeholder='dux://'
          disabled={!!log}
          onChange={(value) => {
            setUrl(() => {
              return value
            })
          }}
        />
      </div>

      <div className='p-4'>
        <div className='mb-2'>{translate('cloud.apps.validator.build')}</div>
        <Switch value={build} onChange={(v) => setBuild(v)} />
        <div className='mt-2 text-placeholder'>{translate('cloud.apps.help.build')}</div>
      </div>

      {log ? (
        <div className='p-4'>
          <pre className='overflow-auto rounded-lg p-4 bg-component'>{log}</pre>
        </div>
      ) : (
        <Modal.Footer>
          <Button
            onClick={() => {
              submit(url)
            }}
          >
            {translate('cloud.apps.action.install')}
          </Button>
        </Modal.Footer>
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
